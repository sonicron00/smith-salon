<?php

namespace App\Services\Booking;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\Staff;
use Carbon\CarbonImmutable;

class SlotService
{
    /** Minimum hours in advance a slot can be booked. */
    private const MIN_ADVANCE_HOURS = 24;

    /** Maximum weeks in advance a slot can be booked. */
    private const MAX_ADVANCE_WEEKS = 6;

    /** Slot granularity in minutes (on the hour and half hour). */
    private const SLOT_INTERVAL = 30;

    /**
     * Return available slot start times, e.g. ["10:00", "10:30"].
     * Uses 30-minute granularity (on the hour and half hour).
     */
    public function availableSlots(Service $service, Staff $staff, string $date, ?int $ignoreAppointmentId = null): array
    {
        $day = CarbonImmutable::parse($date)->startOfDay();
        $now = CarbonImmutable::now();

        // Don't return slots for dates in the past
        if ($day->lt($now->startOfDay())) {
            return [];
        }

        // Don't return slots beyond the max advance window
        $maxDate = $now->addWeeks(self::MAX_ADVANCE_WEEKS)->endOfDay();
        if ($day->gt($maxDate)) {
            return [];
        }

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

        // Also consider recurring blocked times
        $recurringTimeOff = $staff->timeOff()
            ->where('is_recurring', true)
            ->get();

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

        // Apply recurring blocks to this specific day
        foreach ($recurringTimeOff as $recurring) {
            $recurringStart = CarbonImmutable::parse($recurring->starts_at);
            $recurringEnd = CarbonImmutable::parse($recurring->ends_at);
            $recurringWeekday = (int) $recurringStart->dayOfWeekIso - 1;

            if ($recurringWeekday === $weekday) {
                $blockStart = $day->setTimeFromTimeString($recurringStart->format('H:i:s'));
                $blockEnd = $day->setTimeFromTimeString($recurringEnd->format('H:i:s'));
                $blockedRanges[] = [$blockStart, $blockEnd];
            }
        }

        $slots = [];
        $cursor = $this->ceilToHalfHour($start);

        // Enforce 24-hour minimum advance booking
        $earliestBookable = $now->addHours(self::MIN_ADVANCE_HOURS);

        if ($cursor->lt($earliestBookable)) {
            $cursor = $this->ceilToHalfHour($earliestBookable);
        }

        while ($cursor->addMinutes($duration)->lte($end)) {
            $slotStart = $cursor;
            $slotEnd = $cursor->addMinutes($duration);

            if (! $this->overlapsAny($slotStart, $slotEnd, $blockedRanges)) {
                $slots[] = $slotStart->format('H:i');
            }

            $cursor = $cursor->addMinutes(self::SLOT_INTERVAL);
        }

        return $slots;
    }

    public function slotToRange(Service $service, string $date, string $time): array
    {
        $start = CarbonImmutable::parse($date . ' ' . $time);
        $start = $this->ceilToHalfHour($start);
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

        $now = CarbonImmutable::now();
        $earliest = $now->addHours(self::MIN_ADVANCE_HOURS)->startOfDay();
        $latest = $now->addWeeks(self::MAX_ADVANCE_WEEKS)->endOfDay();

        // Clamp the range to the bookable window
        $start = $monthStart->lt($earliest) ? $earliest : $monthStart;
        $end = $monthEnd->gt($latest) ? $latest : $monthEnd;

        if ($start->gt($end)) {
            return [];
        }

        $dates = [];
        $cursor = $start;

        while ($cursor->lte($end)) {
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
        ?int $ignoreAppointmentId = null
    ): ?array {
        $now = CarbonImmutable::now();
        $earliest = $now->addHours(self::MIN_ADVANCE_HOURS)->startOfDay();
        $latest = $now->addWeeks(self::MAX_ADVANCE_WEEKS)->endOfDay();

        $start = CarbonImmutable::parse($fromDate)->startOfDay();

        if ($start->lt($earliest)) {
            $start = $earliest;
        }

        $daysToSearch = (int) $start->diffInDays($latest);

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

    private function ceilToHalfHour(CarbonImmutable $dateTime): CarbonImmutable
    {
        $minute = (int) $dateTime->minute;
        $remainder = $minute % 30;

        if ($remainder === 0) {
            return $dateTime->second(0);
        }

        return $dateTime->addMinutes(30 - $remainder)->second(0);
    }
}