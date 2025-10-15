<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $this->getVar('title') ?></title>
    <style>
        head, body {
            background-color: #fefefe;
            color: #333333;
        }
        .center {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .left {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .margin {
            margin: 1em;
        }
        .fontLarge {
            font-size: xxx-large;
        }
        .fontMedium {
            font-size: x-large;
        }
        .fontNormal {
            font-size: large;
        }
        .fontColor {
            color: #333333;
        }
        .wide {
            width: 50em;
        }
        .form {
            border: 1px solid #333333;
            border-radius: 3px;
            padding: 1em 4em;
        }
        .row {
            border: 1px solid #333333;
            border-radius: 2px;
            padding: 0.5em 2em;
        }
        .field {
            width: 100%;
            margin-bottom: 1em;
        }
        .field input {
            width: 100%;
        }
        .field select {
            font-size: large;
            width: 100%;
            padding: 2px;
        }
        .timeSlots {
            width: 100%;
            margin-bottom: 1em;
        }
        .timeSlots input {
            display: none;
        }
        input[type="radio"]:checked+label {
            margin: 1px;
            border: 2px solid #333333;
        }
        input[type="radio"]:disabled+label {
            border-color: #999999;
            color: #999999;
            cursor: default;
        }
        .timeSlots .dateSlot {
            display: flex;
            flex-wrap: wrap;
            padding-bottom: 1em;
        }
        .timeSlots .dateSlot label {
            border: 1px solid #333333;
            border-radius: 3px;
            cursor: pointer;
            margin: 2px;
            padding: 4px 9px;
            white-space: nowrap;
            display: block;
            width: 87px;
        }
        .error {
            color: #b22700;
        }
        .success {
            color: #008B5E;
        }
    </style>
</head>
<body>