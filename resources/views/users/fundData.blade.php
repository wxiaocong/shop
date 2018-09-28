@forelse($fundList as $val)
<tr>
  <td>{{$val->nickname}}</td>
  <td>{{sprintf("%.2f", ($val->gain-$val->expense)/100)}}</td>
  <td>{{$payTypeArr[$val->pay_type]}}</td>
  <td>{{date('Y-m-d',strtotime($val->created_at))}}</td>
</tr>
@empty
@endforelse
