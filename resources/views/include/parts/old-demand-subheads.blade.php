<style>
    .subhead-details{
        margin: 10px;
        padding: 10px;
        box-shadow: 0px 0px 7px 0px #76797c;
    }
</style>
{{-- @dd($subheads) --}}
@forelse ($subheads as $item)
    <div class="subhead-details">
        @php
            $keys = collect($item)->except(['is_added_to_new_demand','created_at','updated_at','id','DemandID','ComputerCode','BreachType','Floor', 'Area', 'AreaUnit','PaymentType'])->keys()->all();
        @endphp
        <table class="table table-bordered">
            @foreach(array_chunk($keys, 2) as $pair)
            <tr>
                @foreach ($pair as $key)
                <th>{{camelToTitle($key)}}</th>
                @php
                    $value = $item->{$key};
                    if(in_array(strtolower($key),['amount', 'rate'])){
                        $value = '₹ '.customNumFormat($value);
                    }
                    if(strtolower($key) == 'area')
                    {
                        $value = customNumFormat($value);
                    }
                    if(strpos(strtolower($key),'date') !== false && !is_null($value))
                    {
                        $value = date('d-m-Y',strtotime($value));
                    }
                @endphp
                <td>{{ $value }}</td>
                @endforeach
                {{-- If odd number of items, fill the last row --}}
                @if (count($pair) < 2)
                    <th></th><td></td>
                @endif
            </tr>
            @endforeach
        </table>
    </div>
@empty
    <div class="subhead-details">
        <h5>No data to display</h5>
    </div>
@endforelse