<div id="print-footer">
    <table width="100%" style="font-size:12px; line-height:14px;">
        <tr>
            <!-- LEFT: LOGO + COMPANY -->
            <td width="33%" style="text-align:left; vertical-align:middle;">
                <img src="/admin/uploads/logo/{{ $settings->logo }}" style="height:32px; vertical-align:middle;">
                <div style="display:inline-block; vertical-align:middle; margin-left:6px;">
                    <strong>{{ $settings->company_name }}</strong><br>
                    <span style="font-size:11px;">{{ $settings->tag_line }}</span>
                </div>
            </td>

            <!-- CENTER: WEBSITE -->
            <td width="34%" style="text-align:center; vertical-align:middle;">
                {{ $settings->website }}
            </td>

            <!-- RIGHT: PAGE NUMBER -->
            <td width="33%" style="text-align:right;">
                Page
            </td>

        </tr>
    </table>
</div>