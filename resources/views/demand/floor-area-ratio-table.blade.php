@php

    $farGroups = $floorAreaRatios->groupBy(function ($item) {

        $from = $item->effective_from
            ? \Carbon\Carbon::parse($item->effective_from)->format('d-m-Y')
            : null;

        $to = $item->effective_to
            ? \Carbon\Carbon::parse($item->effective_to)->format('d-m-Y')
            : null;

        if (!$from && $to) {
            return 'Up to ' . $to;
        }

        if ($from && !$to) {
            return $from . ' Onwards';
        }

        return $from . ' to ' . $to;
    });

@endphp


@forelse($farGroups as $period => $rows)

    <div class="border rounded mb-3 overflow-hidden">

        {{-- Period --}}
        <div class="bg-light border-bottom px-3 py-2">

            <strong>
                {{ $period }}
            </strong>

        </div>


        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        <th style="width:60px;" class="text-center">
                            #
                        </th>

                        <th>
                            Plot Area
                        </th>

                        <th class="text-center">
                            FAR
                        </th>

                        <th class="text-center">
                            Ground Coverage
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach($rows as $index => $far)

                        @php

                            $unit = $far->area_unit === 'sq_yd'
                                ? 'Sq. Yd.'
                                : 'Sq. Mt.';

                        @endphp

                        <tr>

                            <td class="text-center text-muted">
                                {{ $index + 1 }}
                            </td>


                            <td>

                                @if(is_null($far->area_to))

                                    {{ number_format($far->area_from, 2) }}
                                    {{ $unit }}
                                    and above

                                @else

                                    {{ number_format($far->area_from, 2) }}

                                    -

                                    {{ number_format($far->area_to, 2) }}

                                    {{ $unit }}

                                @endif

                            </td>


                            <td class="text-center fw-semibold">

                                {{ number_format($far->far, 2) }}

                            </td>


                            <td class="text-center">

                                @if(!is_null($far->ground_coverage))

                                    {{ number_format($far->ground_coverage, 2) }}%

                                @else

                                    <span class="text-muted">
                                        -
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

@empty

    <div class="alert alert-warning mb-0">
        No FAR records found.
    </div>

@endforelse