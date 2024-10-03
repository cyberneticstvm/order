@extends("backend.pdf.base")
@section("pdfcontent")
<div class="row">
    <div class="text-center">
        {{ $spectacle?->branch->address }}, {{ $spectacle?->branch->phone }}
    </div>
</div>
<div class="row">
    <div class="col">
        <h4 class="text-center"><u>SPECTACLE PRESCRIPTION</u></h4>
        <table width="100%" cellpadding="0" cellspacing="0" class="bordered">
            <thead>
                <tr>
                    <th text-align="center" colspan="4">PRESCRIPTION</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>CUSTOMER NAME</td>
                    <td>{{ $spectacle?->customer?->name }}</td>
                    <td>AGE / SEX</td>
                    <td>{{ $spectacle?->customer->age }}</td>
                </tr>
                <tr>
                    <td>CUSTOMER ID</td>
                    <td>{{ $spectacle?->customer->ID }}</td>
                    <td>PRESCRIPTION NUMBER</td>
                    <td>{{ $spectacle?->id }}</td>
                </tr>
                <tr>
                    <td>DOCTOR NAME</td>
                    <td>{{ $spectacle?->doctor?->name ?? '' }}</td>
                    <td>ENTRY DATE</td>
                    <td>{{ $spectacle?->created_at->format('d.M.Y h:i a') }}</td>
                </tr>
                <tr>
                    <td>OPTOMETRIST:</td>
                    <td> {{ $spectacle?->optometrist_hospital ?? $spectacle?->optometrists?->name }}</td>
                    <td>MR ID</td>
                    <td>{{ $spectacle?->customer->mrn ?? 'Direct' }}</td>
                </tr>
            </tbody>
        </table>
        <center>
            <p>EYE GLASS PRESCRIPTION</p>
        </center>
        <div class="row">
            <div class="col">
                <table width="100%" class="bordered" cellspacing="0" cellpadding="0">
                    <tr>
                        <td>EYE</td>
                        <td>SPH</td>
                        <td>CYL</td>
                        <td>AXIS</td>
                        <td>ADD</td>
                        <td>VA</td>
                        <td>UC</td>
                    </tr>
                    <tr>
                        <td>RE</td>
                        <td>{{ $spectacle?->re_sph ?? '--' }}</td>
                        <td>{{ $spectacle?->re_cyl ?? '--' }}</td>
                        <td>{{ $spectacle?->re_axis ?? '--' }}</td>
                        <td>{{ $spectacle?->re_add ?? '--' }}</td>
                        <td>{{ $spectacle?->re_va ?? '--' }}</td>
                        <td>{{ $spectacle?->re_uc ?? '--' }}</td>
                    </tr>
                    <tr>
                        <td>LE</td>
                        <td>{{ $spectacle?->le_sph ?? '--' }}</td>
                        <td>{{ $spectacle?->le_cyl ?? '--' }}</td>
                        <td>{{ $spectacle?->le_axis ?? '--' }}</td>
                        <td>{{ $spectacle?->le_add ?? '--' }}</td>
                        <td>{{ $spectacle?->le_va ?? '--' }}</td>
                        <td>{{ $spectacle?->le_uc ?? '--' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row mt-30">
            <table width="100%" class="bordered text-center" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="text-center">VD</td>
                    <td class="text-center">IPD</td>
                    <td class="text-center">NPD</td>
                    <td class="text-center">RPD</td>
                    <td class="text-center">LPD</td>
                </tr>
                <tr>
                    <td class="text-center">{{ $spectacle?->vd ?? '--' }}</td>
                    <td class="text-center">{{ $spectacle?->ipd ?? '--' }}</td>
                    <td class="text-center">{{ $spectacle?->npd ?? '--' }}</td>
                    <td class="text-center">{{ $spectacle?->rpd ?? '--' }}</td>
                    <td class="text-center">{{ $spectacle?->lpd ?? '--' }}</td>
                </tr>
            </table>
        </div>
        <br />
        <div class="row">
            <table width="100%" class="bordered text-center" cellspacing="0" cellpadding="0">
                <thead class="bordered">
                    <tr>
                        <th colspan="4">ARM VALUE</th>
                        <th colspan="6">PGP</th>
                    </tr>
                    <tr>
                        <td></td>
                        <td>SPH</td>
                        <td>CYL</td>
                        <td>AXIS</td>
                        <td>SPH</td>
                        <td>CYL</td>
                        <td>AXIS</td>
                        <td>ADD</td>
                        <td>VISION</td>
                        <td>NV</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>OD</td>
                        <td>{{ $spectacle?->arm_od_sph ?? '--' }}</td>
                        <td>{{ $spectacle?->arm_od_cyl ?? '--' }}</td>
                        <td>{{ $spectacle?->arm_od_axis ?? '--' }}</td>
                        <td>{{ $spectacle?->pgp_od_sph ?? '--' }}</td>
                        <td>{{ $spectacle?->pgp_od_cyl ?? '--' }}</td>
                        <td>{{ $spectacle?->pgp_od_axis ?? '--' }}</td>
                        <td>{{ $spectacle?->pgp_od_add ?? '--' }}</td>
                        <td>{{ $spectacle?->pgp_od_vision ?? '--' }}</td>
                        <td>{{ $spectacle?->pgp_od_nv ?? '--' }}</td>
                    </tr>
                    <tr>
                        <td>OS</td>
                        <td>{{ $spectacle?->arm_os_sph ?? '--' }}</td>
                        <td>{{ $spectacle?->arm_os_cyl ?? '--' }}</td>
                        <td>{{ $spectacle?->arm_os_axis ?? '--' }}</td>
                        <td>{{ $spectacle?->pgp_os_sph ?? '--' }}</td>
                        <td>{{ $spectacle?->pgp_os_cyl ?? '--' }}</td>
                        <td>{{ $spectacle?->pgp_os_axis ?? '--' }}</td>
                        <td>{{ $spectacle?->pgp_os_add ?? '--' }}</td>
                        <td>{{ $spectacle?->pgp_os_vision ?? '--' }}</td>
                        <td>{{ $spectacle?->pgp_os_nv ?? '--' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br />
        <div class="row">
            <table width="100%" class="bordered text-center" cellspacing="0" cellpadding="0">
                <thead class="bordered">
                    <tr>
                        <th colspan="6">CONTACT LENS PRESCRIPTION</th>
                    </tr>
                    <tr>
                        <td></td>
                        <td>BASE CURVE</td>
                        <td>DIAMETER</td>
                        <td>SPH</td>
                        <td>CYL</td>
                        <td>AXIS</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>OD</td>
                        <td>{{ $spectacle?->cl_od_base_curve ?? '--' }}</td>
                        <td>{{ $spectacle?->cl_od_dia ?? '--' }}</td>
                        <td>{{ $spectacle?->cl_od_sph ?? '--' }}</td>
                        <td>{{ $spectacle?->cl_od_cyl ?? '--' }}</td>
                        <td>{{ $spectacle?->cl_od_axis ?? '--' }}</td>
                    </tr>

                    <tr>
                        <td>OS</td>
                        <td>{{ $spectacle?->cl_os_base_curve ?? '--' }}</td>
                        <td>{{ $spectacle?->cl_os_dia ?? '--' }}</td>
                        <td>{{ $spectacle?->cl_os_sph ?? '--' }}</td>
                        <td>{{ $spectacle?->cl_os_cyl ?? '--' }}</td>
                        <td>{{ $spectacle?->cl_os_axis ?? '--' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <table class="border-0 mt-30" width="100%">
        <tr>
            <td class="border-0">
                <p>Ophthalmologist</p>
                <p class="fw-bold">{{ $spectacle?->doctors?->name ?? 'Na' }}</p>
            </td>
            <td class="text-end border-0">
                <p>Optometrist</p>
                <p class="fw-bold">{{ $spectacle?->optometrists?->name ?? 'Na' }}</p>
            </td>
        </tr>
    </table>
    <footer>
        <p>Thank You and Visit Again..</p>
    </footer>
</div>
@endsection