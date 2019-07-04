<?php
    error_log("In remote-call.php");
    $arg1 = isset($_POST['arg1_name']) ? $_POST['arg1_name'] : '';

    error_log("Value of argument_1 = $arg1");
    
    if ($arg1 == '12345') {
        ChainedQuizCompleted :: feedback();
    }
    
    error_log("Finished calling feedback() function in completed.php");