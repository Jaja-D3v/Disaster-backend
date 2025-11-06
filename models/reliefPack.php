<?php
class ReliefPack {
    protected $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    
    public function insert($description, $total_packs, $date_input) {
        if ($total_packs <= 0) {
            throw new Exception("Total packs must be greater than zero");
        }

        $stmt = $this->pdo->prepare("INSERT INTO relief_packs (description, total_packs, date_input, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$description, $total_packs, $date_input]);
        return $this->pdo->lastInsertId();
    }

    
    public function distribute($relief_pack_id, $selected_barangays, $allocation_mode, $based_on = null, $manual_allocations = null) {
        $stmt = $this->pdo->prepare("SELECT * FROM relief_packs WHERE id = ?");
        $stmt->execute([$relief_pack_id]);
        $relief_pack = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$relief_pack) {
            throw new Exception("Relief pack not found");
        }

        $total_packs_available = intval($relief_pack['total_packs']);
        if ($total_packs_available <= 0) {
            
            $stmt = $this->pdo->prepare("DELETE FROM relief_packs WHERE id = ?");
            $stmt->execute([$relief_pack_id]);
            throw new Exception("Relief pack is empty and deleted");
        }

        
        $placeholders = implode(',', array_fill(0, count($selected_barangays), '?'));
        $stmt = $this->pdo->prepare("SELECT id, barangay_name, total_male, total_female, total_families FROM barangay_contact_info WHERE id IN ($placeholders)");
        $stmt->execute($selected_barangays);
        $barangays_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        $found_ids = array_column($barangays_info, 'id');
        $missing_ids = array_diff($selected_barangays, $found_ids);

        if (!empty($missing_ids)) {
            throw new Exception("Selected barangay ID(s) not existing in our system: " . implode(', ', $missing_ids));
        }


        if (empty($barangays_info)) {
            throw new Exception("No valid barangays found");
        }

        $allocations = [];

        if ($allocation_mode === 'manual') {
            if (!is_array($manual_allocations)) {
                throw new Exception("Manual allocations missing");
            }
            $sum = array_sum($manual_allocations);
            if ($sum > $total_packs_available) {
                throw new Exception("Sum of manual allocations exceeds available packs");
            }
            $allocations = $manual_allocations;

        } else { 
            if (!in_array($based_on, ['population','families'])) {
                throw new Exception("Automatic allocation requires 'based_on' parameter (population or families).");
            }

            $total_needed = 0;

            
            foreach ($barangays_info as $b) {
                if ($based_on === 'population') {
                    $count = $b['total_male'] + $b['total_female'];
                } else {
                    $count = $b['total_families'];
                }

                if ($count > $total_packs_available) {
                    throw new Exception("Insufficient relief packs for selected barangays based on $based_on.");
                }

                $allocations[$b['id']] = $count;
                $total_needed += $count;
            }

            if ($total_needed > $total_packs_available) {
                throw new Exception("Insufficient relief packs. Needed: $total_needed, Available: $total_packs_available");
            }
        }

        
        $stmt = $this->pdo->prepare("INSERT INTO relief_pack_barangays (relief_pack_id, barangay_id, allocated_packs) VALUES (?, ?, ?)");
        $total_allocated = 0;
        foreach ($allocations as $barangay_id => $packs) {
            $stmt->execute([$relief_pack_id, $barangay_id, $packs]);
            $total_allocated += $packs;
        }

        
        $remaining_packs = $total_packs_available - $total_allocated;
        if ($remaining_packs <= 0) {
            $stmt = $this->pdo->prepare("DELETE FROM relief_packs WHERE id = ?");
            $stmt->execute([$relief_pack_id]);
        } else {
            $stmt = $this->pdo->prepare("UPDATE relief_packs SET total_packs = ? WHERE id = ?");
            $stmt->execute([$remaining_packs, $relief_pack_id]);
        }

        return [
            'relief_pack_id' => $relief_pack_id,
            'allocated_packs' => $allocations,
            'remaining_packs' => $remaining_packs
        ];
    }


    public function fetchAll() {
        $stmt = $this->pdo->query("SELECT * FROM relief_packs ORDER BY date_input DESC, id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllBarangaysReceived() {
        $stmt = $this->pdo->query("SELECT rpb.*, rp.description AS relief_pack_name
        FROM relief_pack_barangays rpb
        JOIN relief_packs rp ON rpb.relief_pack_id = rp.id
        ORDER BY rpb.created_at DESC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }
}

