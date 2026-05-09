<?php

/**
 * Unified profile status chip for admin member cards (same rules on all pages).
 * Matches Manage Members: PAID = approved + registration fee paid (see allUsers query).
 */
if (!function_exists('admin_member_unified_badge_meta')) {
    /**
     * @param array<string, mixed> $u Expects id, status or user_status, optional registration_fee_paid, registration_fee_queued
     *
     * @return array{variant: string, label: string, icon: string, title: string}
     */
    function admin_member_unified_badge_meta(array $u): array
    {
        $st = strtolower(trim((string) ($u['status'] ?? $u['user_status'] ?? 'approved')));
        if ($st === '') {
            $st = 'approved';
        }
        $regFeePaid = isset($u['registration_fee_paid']) && (int) $u['registration_fee_paid'] === 1;

        if ($st === 'suspended') {
            return [
                'variant' => 'suspended',
                'label' => 'SUSPENDED',
                'icon' => 'fa-user-times',
                'title' => 'Account suspended',
            ];
        }
        if ($st === 'approved' && $regFeePaid) {
            return [
                'variant' => 'paid',
                'label' => 'PAID',
                'icon' => 'fa-check-circle',
                'title' => 'Approved and registration fee paid',
            ];
        }
        if ($st === 'approved') {
            return [
                'variant' => 'approved',
                'label' => 'APPROVED',
                'icon' => 'fa-thumbs-up',
                'title' => 'Approved (registration fee not marked paid)',
            ];
        }
        if ($st === 'unapproved') {
            return [
                'variant' => 'unapproved',
                'label' => 'UNAPPROVED',
                'icon' => 'fa-clock-o',
                'title' => 'Not approved',
            ];
        }

        return [
            'variant' => preg_replace('/[^a-z0-9_-]+/', '', $st) ?: 'unapproved',
            'label' => strtoupper($st),
            'icon' => 'fa-info-circle',
            'title' => 'Profile status',
        ];
    }
}
