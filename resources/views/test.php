<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <span>co1 cai1 nit {{$a}}</span>
    <p>helo {{$b}}</p>
    <p>unsecure {!!$c!!}</p>
    <span>
        @php
            function total($a, $b) {
                return $a + $b;
            }
            $d = total(1, 5);
            echo $d;
        @endphp
    </span>


</body>
</html>