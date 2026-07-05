<div id="print-header" class="section-company">
    <table width="100%">
        <tbody>
            <tr>
                <td style="width: 0%;">
                    <img src="/admin/uploads/logo/logo6941764666973.png" style="width: 100px;height: 100px;">
                </td>
                <td style="width: 40%;vertical-align: middle;text-align: center;">
                    <div style="display:flex; flex-direction:column; justify-content:center;">
                        <h2 style="margin:0;"><b>{{ $settings->company_name }}</b></h2>
                        <h6 style="margin:2px 0 0;"><b>{{ $settings->tag_line }}</b></h6>
                    </div>
                </td>

                <td style="font-size: 12px;line-height: 18px;width: 10%;">
                    <i class="fa fa-envelope" style="margin-right:5px;"></i>{{ $settings->email }}<br>
                    <i class="fa fa-phone" style="margin-right:5px;"></i>{{ $settings->mobile }}<br>
                    <i class="fa fa-id-card" style="margin-right:5px;"></i>{{ $settings->gst_number }}<br>
                    <i class="fa fa-globe" style="margin-right:5px;"></i>{{ $settings->website }}<br>
                    <i class="fa fa-map-marker" style="margin-right:5px;"></i>{{$settings->address}}
                </td>
            </tr>
        </tbody>
    </table>
</div>