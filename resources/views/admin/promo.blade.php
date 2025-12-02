@extends('panel')
@php $baseUrl = 'dicex'; @endphp 

@section('content')
<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default border-panel card-view">
			<div class="panel-heading">
				<div class="pull-left">
					<h6 class="panel-title txt-dark">
						PROMOKODLAR
					</h6>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="panel-wrapper collapse in">
				<div class="panel-body">
                    <div id="createPromo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="createPromoLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h5 class="modal-title" id="myModalLabel">Yeni PROMOKOD</h5>
                                </div>
                                <form action="/{{$baseUrl}}/promoNew" method="post">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="recipient-name" class="control-label mb-10">Kod (yalnız ingilis simvolları):</label>
                                            <input type="text" class="form-control" name="code" placeholder="Код">
                                        </div>
                                        <div class="form-group">
                                            <label for="message-text" class="control-label mb-10">Limit:</label>
                                            <select class="form-control" name="limit">
                                                <option value="0">Limitsiz</option>
                                                <option value="1">Ədədlə</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="recipient-name" class="control-label mb-10">Məbləğ</label>
                                            <input type="text" class="form-control" name="amount" placeholder="Сумма">
                                        </div>
                                        <div class="form-group">
                                            <label for="recipient-name" class="control-label mb-10">Ədəd:</label>
                                            <input type="text" class="form-control" name="count_use" placeholder="Кол-во">
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
                    @foreach($codes as $code)
                    <div id="editPromo{{$code->id}}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="createPromoLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h5 class="modal-title" id="myModalLabel">PROMOD REDAKTE ET</h5>
                                </div>
                                <form action="/{{$baseUrl}}/promoSave" method="post">
                                    <div class="modal-body">
                                        <input type="hidden" value="{{$code->id}}" name="id">
                                        <div class="form-group">
                                            <label for="recipient-name" class="control-label mb-10">Kod (yalnız ingilis simvolları):</label>
                                            <input type="text" class="form-control" name="code" placeholder="Код" value="{{$code->code}}">
                                        </div>
                                        <div class="form-group">
                                            <label for="message-text" class="control-label mb-10">Limit:</label>
                                            <select class="form-control" name="limit">
                                                <option value="0" @if($code->limit == 0) selected @endif>Limitsiz</option>
                                                <option value="1" @if($code->limit == 1) selected @endif>Ədədlə</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="recipient-name" class="control-label mb-10">Məbləğ</label>
                                            <input type="text" class="form-control" name="amount" placeholder="Сумма" value="{{$code->amount}}">
                                        </div>
                                        <div class="form-group">
                                            <label for="recipient-name" class="control-label mb-10">Ədəd:</label>
                                            <input type="text" class="form-control" name="count_use" placeholder="Кол-во" value="{{$code->count_use}}">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Baqlamaq</button>
                                        <button type="submit" class="btn btn-success">Yadda saxla</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <div class="text-center">
                        <a class="btn btn-success btn-rounded" data-toggle="modal" data-target="#createPromo">PROMOKOD YARATMAQ</a>
                    </div>
                    <table id="datable_1" class="table table-hover display  pb-30 dataTable" role="grid" aria-describedby="datable_1_info">
                        <thead>
                            <tr>
                                <th>KOD</th>
                                <th>Limit</th>
                                <th>Məbləğ</th>
                                <th>Ədəd</th>
                                <th>Tədbirlər</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($codes as $code)
                            <tr>
                                <td>{{$code->code}}</td>
                                <td>@if($code->limit) По кол-ву @else Limitsiz @endif</td>
                                <td>{{$code->amount}}pt</td>
                                <td>{{$code->count_use}}</td>
                                <td class="text-center"><a class="btn btn-primary btn-rounded btn-xs" data-toggle="modal" data-target="#editPromo{{$code->id}}">Redakte et/a> / <a href="/{{$baseUrl}}/promoDelete/{{$code->id}}" class="btn btn-danger btn-rounded btn-xs">Sil</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>KOD</th>
                                <th>Limit</th>
                                <th>Məbləğ</th>
                                <th>Ədəd</th>
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