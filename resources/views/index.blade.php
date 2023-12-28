<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.13.3/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
</head>

<body>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <td>#</td>
                    <td>Username</td>
                    <td>Name</td>
                </tr>
            </thead>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.3/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $('table').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                url: "{{ url('/datatable-tester') }}",
                // data: function(d) {
                //     d.email = 'ymaggio@example.com';
                // }
            },
            columns: [{
                data: 'DT_RowIndex',
                searchable: false
            }, {
                data: 'email'
            }, {
                data: 'name'
            }]
        });
    </script>
</body>

</html>
