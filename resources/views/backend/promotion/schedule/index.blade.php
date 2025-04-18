@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Promotion Scheduled Register</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Promotion</li>
                        <li class="breadcrumb-item active">Scheduled Register</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid starts-->
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                <h5>Promotion Scheduled Register</h5><span>Promotion Schedule Management</span>
                            </div>
                            <div class="col text-end">
                                <div class="btn-group">
                                    <button class="btn btn-info dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Add New</button>
                                    <ul class="dropdown-menu dropdown-block">
                                        <li><a class="dropdown-item txt-dark" href="{{ route('promotion.schedule.create') }}">Promotion</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive theme-scrollbar">
                            <table class="display" id="dataTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>Schedule Name</th>
                                        <th>Scheduled Date</th>
                                        <th>Branch</th>
                                        <th>Tamplate ID</th>
                                        <th>Language</th>
                                        <th>Limit</th>
                                        <th>Processed</th>
                                        <th>Status</th>
                                        <th>Status</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($schedules as $key => $schedule)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td><a href="javascript:void(0)" class="waPro" data-drawer="waPromotionDrawer" data-proid="{{ $schedule->id }}">{{ $schedule->name }}</a></td>
                                        <td>{{ $schedule->created_at->format('d.M.Y') }}</td>
                                        <td>{{ $schedule->branch?->name ?? 'All' }}</td>
                                        <td>{{ $schedule->template_id }}</td>
                                        <td>{{ $schedule->template_language }}</td>
                                        <td>{{ $schedule->sms_limit_per_hour }}</td>
                                        <td class="text-center">{{ $schedule->waSmsProcessedCount() }}</td>
                                        <td>{{ ucfirst($schedule->status) }}</td>
                                        <td>{!! $schedule->status() !!}</td>
                                        <td class="text-center"><a href="{{ route('promotion.schedule.edit', encrypt($schedule->id)) }}"><i class="fa fa-edit text-muted fa-lg"></i></a></td>
                                        @if($schedule->deleted_at)
                                        <td class="text-center"><a href="{{ route('promotion.schedule.restore', encrypt($schedule->id)) }}" class="proceed"><i class="fa fa-recycle text-success fa-lg"></i></a></td>
                                        @else
                                        <td class="text-center"><a href="{{ route('promotion.schedule.delete', encrypt($schedule->id)) }}" class="dlt"><i class="fa fa-trash text-danger fa-lg"></i></a></td>
                                        @endif

                                    </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid Ends-->
</div>
@include("backend.misc.wa-promotion-drawer")
@endsection