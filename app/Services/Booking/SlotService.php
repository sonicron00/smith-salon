<?php

namespace App\Services\Booking;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\Staff;
use Carbon\CarbonImmutable;

class SlotService
{
    /**
     * Return available slot start times, e.g. ["10:00", "10:10"].
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
        $end = $day->setTimeFromTimeString($hours->end_time);

        $duration = (int) $service->duration_minutes;
        $buffer = (int) ($service->buffer_minutes ?? 0);

        $timeOff = $staff->timeOff()
            ->where('starts_at', '<', $end)
            ->where('ends_at', '>', $start)
            ->get(['starts_at', 'ends_at']);

        $existing = Appointment::query()
            ->where('staff_id', $staff->id)
            ->where('status', Appointment::STATUS_BOOKED)
            ->when($ignoreAppointmentId, fn ($query) => $query->where('id', '!=', $ignoreAppointmentId))
            ->where('starts_at', '<', $end)
            ->where('ends_at', '>', $start)
            ->get(['starts_at', 'ends_at']);

        $blockedRanges = [];

        foreach ($existing as $appointment) {
            $blockedRanges[] = [
                CarbonImmutable::parse($appointment->starts_at)->subMinutes($buffer),
                CarbonImmutable::parse($appointment->ends_at)->addMinutes($buffer),
            ];
        }

        foreach ($timeOff as $blocked) {
            $blockedRanges[] = [
                CarbonImmutable::parse($blocked->starts_at),
                CarbonImmutable::parse($blocked->ends_at),
            ];
        }

        $slots = [];
        $cursor = $this->ceilToTenMinutes($start);

        $now = CarbonImmutable::now();

        if ($day->isSameDay($now) && $cursor->lt($now)) {
            $cursor = $this->ceilToTenMinutes($now);
        }

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
        $start = CarbonImmutable::parse($date . ' ' . $time);
        $start = $this->ceilToTenMinutes($start);
        $end = $start->addMinutes((int) $service->duration_minutes);

        return [$start, $end];
    }

    public function availableDatesForMonth(
        Service $service,
        Staff $staff,
        string $month,
        ?int $ignoreAppointmentId = null
    ): array {
        $monthStart = CarbonImmutable::parse($month . '-01')->startOfMonth();
        $monthEnd = $monthStart->endOfMonth();

        $dates = [];
        $cursor = $monthStart;

        while ($cursor->lte($monthEnd)) {
            $date = $cursor->toDateString();

            if (! empty($this->availableSlots($service, $staff, $date, $ignoreAppointmentId))) {
                $dates[] = $date;
            }

            $cursor = $cursor->addDay();
        }

        return $dates;
    }

    public function nextAvailableSlot(
        Service $service,
        Staff $staff,
        string $fromDate,
        int $daysToSearch = 90,
        ?int $ignoreAppointmentId = null
    ): ?array {
        $start = CarbonImmutable::parse($fromDate)->startOfDay();
        $today = CarbonImmutable::today();

        if ($start->lt($today)) {
            $start = $today;
        }

        for ($i = 0; $i <= $daysToSearch; $i++) {
            $date = $start->addDays($i)->toDateString();
            $slots = $this->availableSlots($service, $staff, $date, $ignoreAppointmentId);

            if (! empty($slots)) {
                return [
                    'date' => $date,
                    'time' => $slots[0],
                    'label' => CarbonImmutable::parse($date)->format('D j M Y') . ' at ' . $slots[0],
                    'month' => CarbonImmutable::parse($date)->format('Y-m'),
                ];
            }
        }

        return null;
    }

    private function overlapsAny(CarbonImmutable $start, CarbonImmutable $end, array $ranges): bool
    {
        foreach ($ranges as [$rangeStart, $rangeEnd]) {
            if ($rangeStart < $end && $rangeEnd > $start) {
                return true;
            }
        }

        return false;
    }

    private function ceilToTenMinutes(CarbonImmutable $dateTime): CarbonImmutable
    {
        $minute = (int) $dateTime->minute;
        $remainder = $minute % 10;

        if ($remainder === 0) {
            return $dateTime->second(0);
        }

        return $dateTime->addMinutes(10 - $remainder)->second(0);
    }
}