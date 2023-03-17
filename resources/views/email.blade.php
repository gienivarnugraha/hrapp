<!DOCTYPE html>
<html>

<head>
    <title>Training</title>
</head>

<body>
    <h3>Hello {{ $people->name }} ! </h3>

    <p>You have <b> {{ $events->count() }} </b> trainings to attend: </p>

    @foreach ($events as $event)
        <div
            style="padding: 8px !important; 
                   border: 2px dashed rgb(145, 28, 241); 
                   border-radius: 10px;  
                   margin: 10px 0;
                   ">
            <b> {{ $event->title }} </b>
            <p> on: {{ $event->dateForHuman($event->fullStartDate) }} </p>
            <p> until: {{ $event->dateForHuman($event->fullEndDate) }} </p>
        </div>
    @endforeach

    <p>Dont forget to come, and fill the attendance lists! </p>

</body>

</html>
