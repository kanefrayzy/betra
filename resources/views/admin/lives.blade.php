@extends('panel')
@php $baseUrl = 'dicex'; @endphp
@section('content')
<style>
	#act-1, #act-2 {
		display:flex; align-items:center; gap:30px;
	}
</style>
<div class="row" id="act" style="display: block">
	<div class="col-sm-12">
		<div class="panel panel-default border-panel card-view">
			<div class="panel-heading">
			<div id="act-1" onclick="line1()">
					<h6 class="panel-title txt-dark" >Bütün mərclər</h6>
					<button class="btn btn-sm btn-primary" id="active-btn"> Uduşlar  </button>
					</div>
			
				<div class="clearfix"></div>
			</div>
			<div class="panel-wrapper collapse in">
				<div class="panel-body">
                    <table id="datable_1" class="table table-hover display  pb-30 dataTable" role="grid" aria-describedby="datable_1_info">
                        <thead>
                            <tr>
                                <th>ID</th>
								<th>TRX_ID</th>
                                <th>İstifadəçi</th>
                                <th>Məbləğ</th>
                                <th>Status</th>
                                <th>oyuna qədər balans</th>
								<th>oyundan sonra balans</th>
								<th>Tarix</th>
         
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lives as $bets)
                            <tr>
                                <td>{{$bets['id']}}</td>
								<td>{{$bets['trx_id']}}</td>
                                <td><a href="/{{$baseUrl}}/user/{{$bets['user_id']}}"><img src="{{$bets['avatar']}}" style="width:50px;height: 50px;object-fit: cover;border-radius:50%;margin-right:10px;vertical-align:middle;"> {{$bets['username']}}</a></td>
                                <td>{{$bets['amount']}} &#8380;</td>
                                <td>{{$bets['type']}}</td>
                                <td>{{$bets['balanceBefore']}}</td>
								<td>{{$bets['balanceAfter']}}</td>
								<td>{{$bets['created_at']}}</td>
                               
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                           <tr>
                                <th>ID</th>
								<th>TRX_ID</th>
                                <th>İstifadəçi</th>
                                <th>Məbləğ</th>
                                <th>Status</th>
                                <th>oyuna qədər balans</th>
								<th>oyundan sonra balans</th>
								<th>Tarix</th>
         
                            </tr>
                        </tfoot>
                    </table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row" id="obr" style="display: none">
	<div class="col-sm-12">
		<div class="panel panel-default border-panel card-view">
			<div class="panel-heading">
		
				<div id="act-2" onclick="line2()">
					<h6 class="panel-title txt-dark" >
						Uduşlar
					</h6>
					<button class="btn btn-sm btn-primary" id="processed-btn"> Bütün əməliyyatlar  </button>
					</div>
				<div class="clearfix"></div>
			</div>
			<div class="panel-wrapper collapse in">
				<div class="panel-body">
                    <table id="datable_2" class="table table-hover display  pb-30 dataTable" role="grid" aria-describedby="datable_2_info">
                        <thead>
                            <tr>
                                <th>ID</th>
								<th>TRX_ID</th>
                                <th>İstifadəçi</th>
                                <th>Məbləğ</th>
                                <th>Status</th>
                                <th>oyuna qədər balans</th>
								<th>oyundan sonra balans</th>
								<th>Tarix</th>
         
                            </tr>
                        </thead>
                       <tbody>
                            @foreach($wined as $wins)
                            <tr>
                                <td>{{$wins['id']}}</td>
								<td>{{$wins['trx_id']}}</td>
                                <td><a href="/{{$baseUrl}}/user/{{$wins['user_id']}}"><img src="{{$wins['avatar']}}" style="width:50px;height: 50px;object-fit: cover;border-radius:50%;margin-right:10px;vertical-align:middle;"> {{$wins['username']}}</a></td>
                                <td>{{$wins['amount']}} &#8380;</td>
                                <td>{{$wins['type']}}</td>
                                <td>{{$wins['balanceBefore']}}</td>
								<td>{{$wins['balanceAfter']}}</td>
								<td>{{$wins['created_at']}}</td>
                               
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>ID</th>
								<th>TRX_ID</th>
                                <th>İstifadəçi</th>
                                <th>Məbləğ</th>
                                <th>Status</th>
                                <th>oyuna qədər balans</th>
								<th>oyundan sonra balans</th>
								<th>Tarix</th>
         
                            </tr>
                        </tfoot>
                    </table>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	const activePaymentsBtn = document.querySelector('#active-btn');
	const processedPaymentsBtn = document.querySelector('#processed-btn');
	const activePayment = document.querySelector('#act');
	const processedPayments = document.querySelector('#obr');

	activePaymentsBtn.addEventListener('click', function(event) {
		activePayment.style.display = 'none';
		processedPayments.style.display = 'block';
	})

	processedPaymentsBtn.addEventListener('click', function(event) {
		activePayment.style.display = 'block';
		processedPayments.style.display = 'none';
	})
</script>
@endsection