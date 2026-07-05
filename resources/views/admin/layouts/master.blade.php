@extends('layouts.master')

@section('navbar')
  @include('admin.layouts.navbar')
@endsection

@section('footer')
  @include('admin.layouts.footer')
@endsection
@push('scripts')
  <script>
    $(document).ready(function () {
      $('.summernote').summernote({
        height: 200,
        toolbar: [
          ['style', ['bold', 'clear']],
          ['font', ['fontsize']],   // enable font size
          ['para', ['ul', 'ol']],
          ['view', ['codeview']]
        ],
        fontSizes: ['8', '9', '10', '11', '12', '14', '18', '24', '36'], // optional sizes
        disableDragAndDrop: true,
        callbacks: {
          onPaste: function (e) {
            e.preventDefault();
            let text = (e.originalEvent || e).clipboardData.getData('text/plain');
            document.execCommand('insertText', false, text);
          },
          onKeydown: function (e) {
            if (e.keyCode === 13) {
              document.execCommand('insertLineBreak');
              e.preventDefault();
            }
          }
        }
      });
    });
  </script> <!-- DataTables Core JS -->
  <script src="{{ url('/') }}/admin/plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="{{ url('/') }}/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>

  <!-- Responsive -->
  <script src="{{ url('/') }}/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
  <script src="{{ url('/') }}/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

  <!-- Buttons -->
  <script src="{{ url('/') }}/admin/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
  <script src="{{ url('/') }}/admin/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>

  <!-- Buttons: Export Files -->
  <script src="{{ url('/') }}/admin/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
  <script src="{{ url('/') }}/admin/plugins/datatables-buttons/js/buttons.print.min.js"></script>
  <script src="{{ url('/') }}/admin/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

  <!-- Required for Excel/PDF export -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
  <script>

    $(document).ready(function () {
      $('#example1').DataTable({
        responsive: true,
        autoWidth: false,
        lengthChange: true,
        paging: true,
        searching: true,
        ordering: true,
        dom: 'Bfrtip',
        dom:
          "<'row'<'col-sm-6'B><'col-sm-6'f>>" +
          "<'row'<'col-sm-12'tr>>" +
          "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
          { extend: 'colvis', className: 'btn btn-secondary' }
        ]
      });
    });
  </script>
@endpush