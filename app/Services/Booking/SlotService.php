<?php

namespace App\Services\Booking;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\Staff;
use Carbon\CarbonImmutable;

class SlotService
{
    /**
     * Return available slot start times (HH:MM) for a given date.
     * Uses 10-minute granularity.
     */
    public function availableSlots(Service $service, Staff $staff, string $date, ?int $ignoreAppointmentId = null): array
    {
        $day = CarbonImmutable::parse($date)->startOfDay();
        $weekday = (int) $day->dayOfWeekIso - 1; // 0=Mon

        $hours = $staff->workingHours()->where('weekday', $weekday)->first();
        if (! $hours) {
            return [];
        }

        $start = $day->setTimeFromTimeString($hours->start_time);
        $end   = $day->setTimeFromTimeString($hours->end_time);

        // Apply time off blocks
        $timeOff = $staff->timeOff()
            ->where('starts_at', '<', $end)
            ->where('ends_at', '>', $start)
            ->get(['starts_at','ends_at']);

        // Existing appointments (including buffers)
        $duration = (int) $service->duration_minutes;
        $buffer = (int) ($service->buffer_minutes ?? 0);

        $existing = Appointment::query()
            ->where('staff_id', $staff->id)
            ->where('status', Appointment::STATUS_BOOKED)
            ->when($ignoreAppointmentId, fn($q) => $q->where('id', '!=', $ignoreAppointmentId))
            ->where('starts_at', '<', $end)
            ->where('ends_at', '>', $start)
            ->get(['starts_at','ends_at']);

        $blockedRanges = [];

        foreach ($existing as $a) {
            $blockedRanges[] = [
                CarbonImmutable::parse($a->starts_at)->subMinutes($buffer),
                CarbonImmutable::parse($a->ends_at)->addMinutes($buffer),
            ];
        }

        foreach ($timeOff as $o) {
            $blockedRanges[] = [
                CarbonImmutable::parse($o->starts_at),
                CarbonImmutable::parse($o->ends_at),
            ];
        }

        $slots = [];
        $cursor = $this->ceilToTenMinutes($start);

        while ($cursor->addMinutes($duration)->lte($end)) {
            $slotStart = $cursor;
            $slotEnd = $cursor->addMinutes($duration);

            if (! $this->overlapsAny($slotStart, $slotEnd, $blockedRanges)) {
                $slots[] = $slotStart->format('H:i');
            }

            $cursor = $cursor->addMinutes(10);
        }

        return $slots;
    }

    public function slotToRange(Service $service, string $date, string $time): array
    {
        $start = CarbonImmutable::parse($date.' '.$time);
        $start = $this->ceilToTenMinutes($start); // normalize
        $end = $start->addMinutes((int) $service->duration_minutes);
        return [$start, $end];
    }

    private function overlapsAny(CarbonImmutable $start, CarbonImmutable $end, array $ranges): bool
    {
        foreach ($ranges as [$a, $b]) {
            if ($a < $end && $b > $start) {
                return true;
            }
        }
        return false;
    }

    private function ceilToTenMinutes(CarbonImmutable $dt): CarbonImmutable
    {
        $minute = (int) $dt->minute;
        $remainder = $minute % 10;
        if ($remainder === 0) return $dt->second(0);

        return $dt->addMinutes(10 - $remainder)->second(0);
    }
}
