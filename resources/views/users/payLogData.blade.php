@forelse($payLog as $val)
<tr>
  <td>{{$payTypeArr[$val->pay_type]}}</td>
  <td>{{sprintf("%.2f", $val->gain/100)}}</td>
  <td>{{sprintf("%.2f", $val->expense/100)}}</td>
  <td>{{sprintf("%.2f", $val->balance/100)}}</td>
  <td>{{date('Y-m-d',strtotime($val->created_at))}}</td>
</tr>
@empty
@endforelse
