@extends('panel')
@php $baseUrl = 'dicex'; @endphp 
@section('content')
<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default border-panel card-view">
			<div class="panel-heading">
				<div class="pull-left">
					<h6 class="panel-title txt-dark">
						Ödəniş nömrələri
					</h6>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="panel-wrapper collapse in">
				<div class="panel-body">
                    <div id="createBonus" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="createBonusLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h5 class="modal-title" id="myModalLabel">Pul kisəsi əlavə edin</h5>
                                </div>
                                <form action="/{{$baseUrl}}/walletAdd" method="post">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="recipient-name" class="control-label mb-10">Nomer</label>
                                            <input type="text" class="form-control" name="wallet" placeholder="Nomer">
                                        </div>
                                        <div class="form-group">
                                            <label for="message-text" class="control-label mb-10">System</label>
                                            <select class="form-control" name="system">
                                                <option value="0">M10</option>
                                                <option value="1">Karta</option>
												<option value="2">MPAY</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Baqlamaq</button>
                                        <button type="submit" class="btn btn-success">Yaratmaq</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @foreach($wallets as $bonus)
                    <div id="editBonus{{$bonus->id}}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="createBonusLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h5 class="modal-title" id="myModalLabel">Ödəniş nömrələri redaktə etmək</h5>
                                </div>
                                <form action="/{{$baseUrl}}/bonusSave" method="post">
                                    <div class="modal-body">
                                        <input type="hidden" value="{{$bonus->id}}" name="id">
                                        <div class="form-group">
                                            <label for="recipient-name" class="control-label mb-10">Nömrə</label>
                                            <input type="text" class="form-control" name="sum" placeholder="Сумма" value="{{$bonus->sum}}">
                                        </div>
                                        <div class="form-group">
                                            <label for="message-text" class="control-label mb-10">Sistem</label>
                                            <select class="form-control" name="status">
                                                <option value="0">M10</option>
                                                <option value="1">Karta</option>
												<option value="2">MPAY</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Baqlamaq</button>
                                        <button type="submit" class="btn btn-success">Yaratmaq</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <div class="text-center">
                        <a class="btn btn-success btn-rounded" data-toggle="modal" data-target="#createBonus">Pul kisəsi əlavə edin</a>
                    </div>
                    <table id="datable_1" class="table table-hover display  pb-30 dataTable" role="grid" aria-describedby="datable_1_info">
                        <thead>
                            <tr>
                                <th>Nömrə</th>
                                <th>Sistem</th>
                                <th>Tədbirlər</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($wallets as $wallet)
                            <tr>
                                <td>{{$wallet->wallet}}</td>
                                <td>@if($wallet->system == 0) M10 @elseif($wallet->system == 1) Karta @else MPAY @endif</td>
                                <td class="text-center"><a href="/{{$baseUrl}}/walletDelete/{{$wallet->id}}" class="btn btn-danger btn-rounded btn-xs">Sil</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Nömrə</th>
                                <th>Sistem</th>
                                <th>Tədbirlər</th>
                            </tr>
                        </tfoot>
                    </table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection