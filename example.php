<?php
/**
 * Example webpage for ZnPhp_Form using Twitter Bootstrap 3
 *
 * For browsers that do not support <input type="date"> and <input type="time">,
 * see https://github.com/xdan/datetimepicker
 *
 * @author Zion Ng <zion@intzone.com>
 * @link   https://github.com/zionsg/ZnPhp-Form for canonical source repository
 * @since  2014-11-05T13:00+08:00
 */

include 'ZnPhp_Form.php';

$form = new ZnPhp_Form(array(
    'groups' => array(
        'personal_details' => array(
            'label' => 'Personal Details',
            'elements' => array('salutation', 'first_name', 'birth_date', 'gender'),
        ),
        'other_details' => array(
            'label' => 'Other Details',
            'elements' => array('vehicles', 'writeup', 'submit'),
        ),
    ),
    'elements' => array(
        'salutation' => array(
            'label' => 'Salutation',
            'type' => 'select',
            'options' => array('' => 'Please select salutation', 'value-mr' => 'Mr', 'value-miss' => 'Miss'),
        ),
        'first_name' => array(
            'required' => true,
            'label' => 'First Name',
            'description' => 'No numbers allowed',
            'errorMessage' => 'Please enter valid name',
            'validator' => function ($value) {
                return (preg_match('/^[a-zA-Z ]+$/', $value) ? true : false); // preg_match returns 1 if match
            },
        ),
        'birth_date' => array(
            'label' => 'Date of Birth',
            'type' => 'date',
        ),
        'gender' => array(
            'label' => 'Gender',
            'type' => 'radio',
            'value' => 'value-male',
            'options' => array('value-male' => 'Male', 'value-female' => 'Female'),
        ),
        'vehicles' => array(
            'label' => 'Vehicles owned',
            'type' => 'checkbox',
            'value' => array('value-car', 'value-bike'),
            'options' => array('value-car' => 'Car', 'value-bike' => 'Bike'),
        ),
        'writeup' => array(
            'label' => 'Write-up',
            'type' => 'textarea',
            'placeholder' => 'Please write something about yourself',
            'attributes' => array('rows' => 5),
        ),
        'submit' => array(
            'type' => 'submit',
            'value' => 'Submit Form',
            'elementClass' => 'btn btn-default',
        ),
    ),
));

$isValid = null;
if (isset($_POST['submit'])) {
    $data = $_POST;
    $isValid = $form->isValid($data);
}
?>
<html>

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <!-- <link rel="stylesheet" href="https://raw.githubusercontent.com/xdan/datetimepicker/master/jquery.datetimepicker.css"> -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

    <style>
      .required { color: red; }
      .description { font-style: italic; }
      .error { color: red; }

      .group-title {
        background-color: #808080;
        color: white;
        padding: 10px;
      }
    </style>
  </head>

  <body>
    <br /><br />

    <div class="row">
      <div class="col-sm-6 col-sm-offset-3">
        <?php if (true === $isValid): ?>
          <div class="alert alert-success text-center">
            Thank you for submitting the form! Here are the values you submitted:<br /><br />
            <pre class="text-left"><?php print_r($data); ?></pre>
          </div>
        <?php elseif (false === $isValid): ?>
          <div class="alert alert-danger text-center">There are errors in the form</div>
        <?php endif; ?>

        <form action="" method="post" class="form-horizontal">
          <?php
          foreach ($form->getGroups() as $name => $group) {
              if ($group['label']) {
                  printf('<div class="group-title">%s</div><br />' . "\n", $group['label']);
              }
              printf(
                  '<div id="%s">%s</div>' . "\n",
                  $name,
                  $form->renderElements($group['elements'])
              );
          }
          ?>
        </form>
      </div>
    </div>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <!-- <script src="https://raw.githubusercontent.com/xdan/datetimepicker/master/jquery.datetimepicker.js"></script> -->
    <script>
      jQuery(document).ready(function ($) {
          // $('input[type="date"], input[type="time"]').datetimepicker();
      });
    </script>
  </body>
</html>
