<?php

namespace App\Http\Controllers;

use App\Models\JobOrder;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $today = CarbonImmutable::today('Asia/Manila');
        $month = $today->startOfMonth();
        $monthInput = $request->query('month');

        if (is_string($monthInput) && preg_match('/^([1-9][0-9]{3})-(0[1-9]|1[0-2])$/D', $monthInput)
            && (int) substr($monthInput, 0, 4) < 9999) {
            $month = CarbonImmutable::createFromFormat('!Y-m', $monthInput, 'Asia/Manila');
        }

        $selectedDay = $today->isSameMonth($month) ? $today : $month;
        $dayInput = $request->query('day');
        if (is_string($dayInput) && preg_match('/^'.preg_quote($month->format('Y-m'), '/').'-(0[1-9]|[12][0-9]|3[01])$/D', $dayInput)) {
            $candidate = $month->setDay((int) substr($dayInput, -2));
            if ($candidate->format('Y-m-d') === $dayInput) {
                $selectedDay = $candidate;
            }
        }

        $gridStart = $month->startOfWeek(CarbonInterface::SUNDAY);
        $gridEnd = $month->endOfMonth()->endOfWeek(CarbonInterface::SATURDAY)->startOfDay();
        $orders = JobOrder::with('assignees:id,name,position')->where(function (Builder $query) use ($gridStart, $gridEnd): void {
            $query->where(function (Builder $query) use ($gridStart, $gridEnd): void {
                $query->where('created_at', '>=', $gridStart->utc()->format('Y-m-d H:i:s'))
                    ->where('created_at', '<', $gridEnd->addDay()->utc()->format('Y-m-d H:i:s'));
            })->orWhereBetween('due_date', [$gridStart->toDateString(), $gridEnd->toDateString()]);
        })->orderBy('title')->orderBy('id')->get();

        /** @var array<string, list<array{kind: string, job: JobOrder}>> $events */
        $events = [];
        $totals = ['start' => 0, 'due' => 0];
        foreach ($orders as $order) {
            $dates = ['start' => $order->created_at->copy()->timezone('Asia/Manila')->toDateString()];
            if ($order->due_date) {
                $dates['due'] = $order->due_date->toDateString();
            }
            foreach ($dates as $kind => $date) {
                if ($date >= $gridStart->toDateString() && $date <= $gridEnd->toDateString()) {
                    $events[$date][] = ['kind' => $kind, 'job' => $order];
                    if (str_starts_with($date, $month->format('Y-m'))) {
                        $totals[$kind]++;
                    }
                }
            }
        }

        $days = [];
        for ($day = $gridStart; $day->lte($gridEnd); $day = $day->addDay()) {
            $dayEvents = $events[$day->toDateString()] ?? [];
            $days[] = [
                'date' => $day,
                'starts' => count(array_filter($dayEvents, fn (array $event): bool => $event['kind'] === 'start')),
                'due' => count(array_filter($dayEvents, fn (array $event): bool => $event['kind'] === 'due')),
            ];
        }

        return view('admin-dashboard', [
            'calendarMonth' => $month,
            'calendarToday' => $today,
            'calendarSelectedDay' => $selectedDay,
            'calendarWeeks' => array_chunk($days, 7),
            'calendarEvents' => $events[$selectedDay->toDateString()] ?? [],
            'calendarTotals' => $totals,
        ]);
    }
}
