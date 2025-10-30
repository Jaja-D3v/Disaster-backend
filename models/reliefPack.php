<?php
class ReliefPack {
    protected $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Insert a new relief pack
    public function insert($description, $total_packs, $date_input) {
        if ($total_packs <= 0) {
            throw new Exception("Total packs must be greater than zero");
        }

        $stmt = $this->pdo->prepare("INSERT INTO relief_packs (description, total_packs, date_input, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$description, $total_packs, $date_input]);
        return $this->pdo->lastInsertId();
    }

    // Distribute packs
    public function distribute($relief_pack_id, $selected_barangays, $allocation_mode, $based_on = null, $manual_allocations = null) {
        $stmt = $this->pdo->prepare("SELECT * FROM relief_packs WHERE id = ?");
        $stmt->execute([$relief_pack_id]);
        $relief_pack = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$relief_pack) {
            throw new Exception("Relief pack not found");
        }

        $total_packs_available = intval($relief_pack['total_packs']);
        if ($total_packs_available <= 0) {
            // Delete empty pack
            $stmt = $this->pdo->prepare("DELETE FROM relief_packs WHERE id = ?");
            $stmt->execute([$relief_pack_id]);
            throw new Exception("Relief pack is empty and deleted");
        }

        // Fetch barangays
        $placeholders = implode(',', array_fill(0, count($selected_barangays), '?'));
        $stmt = $this->pdo->prepare("SELECT id, barangay_name, total_male, total_female, total_families FROM barangay_contact_info WHERE id IN ($placeholders)");
        $stmt->execute($selected_barangays);
        $barangays_info = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

            // Calculate total count
            $total_count = 0;
            $barangays_info_map = [];
            foreach ($barangays_info as $b) {
                $count = ($based_on === 'population') ? ($b['total_male'] + $b['total_female']) : $b['total_families'];
                $barangays_info_map[$b['id']] = $count;
                $total_count += $count;
            }

            if ($total_packs_available < $total_count) {
                throw new Exception("Insufficient relief packs. Required: $total_count, Available: $total_packs_available");
            }

            // Allocate proportionally
            foreach ($barangays_info_map as $b_id => $count) {
                $allocations[$b_id] = floor(($count / $total_count) * $total_packs_available);
            }

            // Distribute remainder
            $sum_allocated = array_sum($allocations);
            $remainder = $total_packs_available - $sum_allocated;
            foreach ($barangays_info_map as $b_id => $count) {
                if ($remainder <= 0) break;
                $allocations[$b_id] += 1;
                $remainder--;
            }
        }

        // Insert allocations
        $stmt = $this->pdo->prepare("INSERT INTO relief_pack_barangays (relief_pack_id, barangay_id, allocated_packs) VALUES (?, ?, ?)");
        $total_allocated = 0;
        foreach ($allocations as $barangay_id => $packs) {
            $stmt->execute([$relief_pack_id, $barangay_id, $packs]);
            $total_allocated += $packs;
        }

        // Deduct from relief pack
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

    // Fetch all relief packs
    public function fetchAll() {
        $stmt = $this->pdo->query("SELECT * FROM relief_packs ORDER BY date_input DESC, id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
