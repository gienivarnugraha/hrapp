<!DOCTYPE html>
<html>

<head>
    <title>Training</title>
</head>

<body>
    <h3>Hello {{ $people->name }} ! </h3>

    <p>You have <b> {{ $people->events()->count() }} </b> trainings to attend: </p>

    @foreach ($people->events as $event)
        <b> {{ $event->title }} </b>
        <p> on: {{ $event->dateForHuman($event->fullStartDate) }} </p>
        <p> until: {{ $event->dateForHuman($event->fullEndDate) }} </p>
        <p> ================================================== </p>
    @endforeach

    <p>Dont forget to come, and fill the attendance lists! </p>

</body>

</html>
