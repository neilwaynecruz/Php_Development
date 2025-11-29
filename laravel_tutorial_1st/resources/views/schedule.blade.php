<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PUP Schedule Encoding</title>


    <link rel="stylesheet" href="{{ asset('css/pup-theme.css') }}">
</head>
<body>

    <div class="card">
        <div class="card-header">
            <h2>Subject Schedule</h2>
            <p>Polytechnic University of the Philippines</p>
        </div>

        <div class="card-body">

            <!-- Success Message Check -->
            @if(session('success'))
                <div class="alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form method="post" action="{{ url('/save-schedule') }}">
                @csrf

                <div class="form-group">
                    <label>Subject Code</label>
                    <input type="text" name="subjcode" placeholder="e.g., COMP 20133" required>
                </div>

                <div class="form-group">
                    <label>Day/Time</label>
                    <input type="text" name="day_time" placeholder="e.g., Mon/Thu 7:30 AM" required>
                </div>

                <div class="form-group">
                    <label>Professor</label>
                    <input type="text" name="professor" placeholder="e.g., Inst. Dela Cruz" required>
                </div>

                <div>
                    <input type="submit" name="btnSave" value="Save Schedule" class="btn-submit">
                </div>
            </form>
        </div>
    </div>

</body>
</html>
