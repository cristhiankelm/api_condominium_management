<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api\Area;
use App\Models\Api\AreaDisabledDay;
use App\Models\Api\Reservation;
use App\Models\Api\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    public function getReservations()
    {
        $array = ['error' => '', 'list' => []];
        $daysHelper = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

        $areas = Area::where('allowed', 1)->get();

        foreach ($areas as $area) {
            $dayList = explode(',', $area['days']);

            $dayGroups = [];

            $lastDay = intval(current($dayList));
            $dayGroups[] = $daysHelper[$lastDay];
            array_shift($dayList);

            foreach ($dayList as $day) {
                if (intval($day) != $lastDay + 1) {
                    $dayGroups[] = $daysHelper[$lastDay];
                    $dayGroups[] = $daysHelper[$day];
                }
                $lastDay = intval($day);
            }

            $dayGroups[] = $daysHelper[end($dayList)];

            $dates = '';
            $close = 0;
            foreach ($dayGroups as $group) {
                if ($close === 0) {
                    $dates .= $group;
                } else {
                    $dates .= '-' . $group . ',';
                }
                $close = 1 - $close;
            }

            $dates = explode(',', $dates);
            array_pop($dates);

            $start = date('H:i', strtotime($area['start_time']));
            $end = date('H:i', strtotime($area['end_time']));

            foreach ($dates as $dKey => $dValue) {
                $dates[$dKey] .= ' ' . $start . ' às ' . $end;
            }

            $array['list'][] = [
                'id' => $area['id'],
                'cover' => asset('storage/' . $area['cover']),
                'title' => $area['title'],
                'dates' => $dates
            ];
        }

        return $array;
    }

    public function setReservation(Request $request, $id)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i:s',
            'property' => 'required'
        ]);

        if (!$validator->fails()) {
            $date = $request->input('date');
            $time = $request->input('time');
            $property = $request->input('property');

            $unit = Unit::find($property);
            $area = Area::find($id);

            if ($unit && $area) {
                $can = true;
                $weekday = date('w', strtotime($date));

                //Verificar se está dentro da disponibilidade padrão
                $allowedDays = explode(',', $area['days']);
                if (!in_array($weekday, $allowedDays)) {
                    $can = false;
                } else {
                    $start = strtotime($area['start_time']);
                    $end = strtotime('-1 hour', strtotime($area['end_time']));
                    $revtime = strtotime($time);
                    if ($revtime < $start || $revtime > $end) {
                        $can = false;
                    }
                }

                //Verificar se esta dentro dos DisabledDays
                $existingDisabledDay = AreaDisabledDay::where('area_id', $id)
                    ->where('day', $date)
                    ->count();
                if ($existingDisabledDay > 0) {
                    $can = false;
                }

                // Verificar se nao existe outra reserva no mesmo dia/hora
                $existingReservations = Reservation::where('area_id', $id)
                    ->where('reservation_date', $date . ' ' . $time)
                    ->count();
                if ($existingReservations > 0) {
                    $can = false;
                }

                //Se todas as condições anteriores forem cumpridas então
                if ($can) {
                    $newReservation = new Reservation();
                    $newReservation->unit_id = $property;
                    $newReservation->area_id = $id;
                    $newReservation->reservation_date = $date . ' ' . $time;
                    $newReservation->save();
                } else {
                    $array['error'] = 'Reserva não permitida neste dia/horário';
                    return $array;
                }
            } else {
                $array['error'] = 'Dados incorretos';
                return $array;
            }

        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function getDisabledDays($id)
    {
        $array = ['error' => '', 'list' => []];

        $area = Area::find($id);

        if ($area) {
            //Dias disabled padrão
            $disabledDays = AreaDisabledDay::where('area_id', $id)->get();
            foreach ($disabledDays as $disabledDay) {
                $array['list'][] = $disabledDay['day'];
            }

            //Dias disabled através do allowed
            $allowedDays = explode(',', $area['days']);
            $offDays = [];
            for ($i = 0; $i < 7; $i++) {
                if (!in_array($i, $allowedDays)) {
                    $offDays[] = $i;
                }
            }

            //Listar os dias proibidos +3 meses
            $start = time();
            $end = strtotime('+3 months');

            for (
                $current = $start;
                $current < $end;
                $current = strtotime('+1 day', $current)
            ) {
                $weekDay = date('w', $current);
                if (in_array($weekDay, $offDays)) {
                    $array['list'][] = date('Y-m-d', $current);
                }
            }
        } else {
            $area['error'] = 'Area inexistente';
            return $array;
        }
        return $array;
    }

    public function getTimes(Request $request, $id)
    {
        $array = ['error' => '', 'list' => []];

        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d'
        ]);
        if (!$validator->fails()) {
            $date = $request->input('date');
            $area = Area::find($id);

            if ($area) {
                $can = true;

                //Verificar se é dia disabled
                $existingDisabledDays = AreaDisabledDay::where('area_id', $id)
                    ->where('day', $date)
                    ->count();
                if ($existingDisabledDays > 0) {
                    $can = false;
                }

                //Verificar se é dia permitido
                $allowedDays = explode(',', $area['days']);
                $weekDays = date('w', strtotime($date));
                if (!in_array($weekDays, $allowedDays)) {
                    $can = false;
                }

                if ($can) {
                    $start = strtotime($area['start_time']);
                    $end = strtotime($area['end_time']);
                    $times = [];

                    for (
                        $lastTime = $start;
                        $lastTime < $end;
                        $lastTime = strtotime('+1 hour', $lastTime)
                    ) {
                        $times[] = $lastTime;
                    }

                    $timeList = [];
                    foreach ($times as $time) {
                        $timeList[] = [
                            'id' => date('H:i:s', $time),
                            'title' => date('H:i', $time) . ' - ' . date('H:i', strtotime('+1 hour', $time))
                        ];
                    }

                    //Removendo as reservas
                    $reservations = Reservation::where('area_id', $id)
                        ->whereBetween('reservation_date', [
                            $date . ' 00:00:00',
                            $date . ' 23:59:00'
                        ])
                        ->get();

                    $toRemove = [];
                    foreach ($reservations as $reservation) {
                        $time = date('H:i:s', strtotime($reservation['reservation_date']));
                        $toRemove[] = $time;
                    }

                    foreach ($timeList as $timeItem) {
                        if (!in_array($timeItem['id'], $toRemove)) {
                            $array['list'][] = $timeItem;
                        }
                    }
                }

            } else {
                $array['error'] = 'Area inexistente';
                return $array;
            }

        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function getMyReservations(Request $request)
    {
        $array = ['error' => '', 'list' => []];

        $property = $request->input('property');
        if ($property) {
            $unit = Unit::find($property);
            if ($unit) {
                $reservations = Reservation::where('unit_id', $property)
                    ->orderBy('reservation_date', 'DESC')
                    ->get();

                foreach ($reservations as $reservation) {
                    $area = Area::find($reservation['area_id']);
                    $dateReservation = date('d/m/Y H:i', strtotime($reservation['reservation_date']));
                    $afterTime = date('H:i', strtotime('+1 hour', strtotime($reservation['reservation_date'])));

                    $dateReservation .= ' à ' . $afterTime;

                    $array['list'][] = [
                        'id' => $reservation['id'],
                        'area_id' => $reservation['area_id'],
                        'title' => $area['title'],
                        'cover' => asset('storage/' . $area['cover']),
                        'dateReserved' => $dateReservation
                    ];
                }
            } else {
                $array['error'] = 'Propriedade inexistente';
                return $array;
            }
        } else {
            $array['error'] = 'Propriedade necessária';
            return $array;
        }

        return $array;
    }
}
