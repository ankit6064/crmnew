@extends('layouts.dt')

@section('content')
    <table id="posts-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>User Id</th>
                <th>Source</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>

    <script>
        $(function() {
            // $('#posts-table').DataTable({
            //     processing: true,
            //     serverSide: true,
            //     ajax: '{{ route('test.datatables') }}',
            //     columns: [{
            //             data: 'id',
            //             name: 'id'
            //         },
            //         {
            //             data: 'user_id',
            //             name: 'user_id'
            //         },
            //         {
            //             data: 'source_name',
            //             name: 'source_name'
            //         },
            //         {
            //             data: 'actions',
            //             name: 'actions',
            //             orderable: false,
            //             searchable: false
            //         }
            //     ]
            // });
        });
    </script>
@endsection
