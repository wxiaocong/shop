@forelse($record as $val)
<tr>
  <td>{{$val->order_sn}}</td>
  <td>{{sprintf("%.2f", $val->amount/100)}}</td>
  <td>{{sprintf("%.2f", $val->cmms_amt/100)}}</td>
  <td>@if($val->state==1) 等待付款 @elseif($val->state==2) 提现成功 @else 取消 @endif</td>
  <td>{{$val->pay_time}}</td>
</tr>
@empty
@endforelse
