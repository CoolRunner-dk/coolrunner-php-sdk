<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('error_reporting', E_ALL);

//header('Content-Type: application/json');

require_once '../vendor/autoload.php';

//$sps = $api->findServicepoints('GLS', 'DK', '9000');
//$sp = $api->getServicepoint(\CoolRunnerSDK\API::CARRIER_GLS, 95809);

//var_dump($api->findServicepoints('dao', 'dk', '6000'));

//var_dump($sp);
//
//$prd = $api->getProducts('DK');

//var_dump($prd->getCountry('dk')->getCarrier('dao')->getType('private'));

$sender = new \CoolRunnerSDK\Models\Properties\Person(
    array(
        'name'      => 'Morten Harders',
        'attention' => '',
        'street1'   => 'Stationsvej 24B',
        'street2'   => '',
        'zipcode'   => '9440',
        'city'      => 'Aabybro, Biersted',
        'country'   => 'DK',
        'phone'     => '28626622',
        'email'     => 'mh@coolrunner.dk'
    )
);
$receiver = new \CoolRunnerSDK\Models\Properties\Person(
    array(
        'name'         => 'Ane Kornum',
        'attention'    => '',
        'street1'      => 'Lerbjergvej 17',
        'street2'      => '',
        'zipcode'      => '6000',
        'city'         => 'Kolding',
        'country'      => 'DK',
        'phone'        => '61718670',
        'email'        => 'mmh@harders-it.dk',
        'notify_sms'   => '61718670',
        'notify_email' => 'mmh@harders-it.dk'
    )
);

$length = 20;
$width = 30;
$height = 20;
$weight = 500;

$shipment = new \CoolRunnerSDK\Models\Shipments\Shipment(
    array(
        'sender'          => $sender,
        'receiver'        => $receiver,
        'length'          => $length,
        'width'           => $width,
        'height'          => $height,
        'weight'          => $weight,
        'carrier'         => 'dao',
        'carrier_service' => 'droppoint',
        'carrier_product' => 'private',
        'reference'       => 'Test shipment',
        'description'     => 'This is a test',
        'comment'         => 'Plz ignore this',
        'label_format'    => 'LabelPrint',
        'droppoint_id'    => '47020'
    )
);

//var_dump($shipment->getPossibleProducts());

//$resp = $shipment->create();

//var_dump($resp);

//$resp = \CoolRunnerSDK\Models\Shipments\ShipmentInfo::test_get_from_cache('00057126960221449174');

//$api = CoolRunnerSDK\APILight::load('kq+firma@tric.dk', '7exr3iepkpeh60l6vzfwk7qyflhped3t', null, \CoolRunnerSDK\APILight::OUTPUT_MODE_ASSOC);
$api = CoolRunnerSDK\API::load('kq+firma@tric.dk', '7exr3iepkpeh60l6vzfwk7qyflhped3t');
//$pcn = \CoolRunnerSDK\PCN::load('kq+firma@tric.dk', '7exr3iepkpeh60l6vzfwk7qyflhped3t');

//var_dump($api->findServicepoints('dao', 'dk', '9000'));

$json = '{
    "package_number": "00058126960004762308",
  "carrier": "dao",
  "tracking":
  [
    {
        "timestamp": "2018-01-12 12:00",
      "title": "Electronic data received",
      "event": "received",
      "location": ""
    },
    {
        "timestamp": "2018-01-13 16:00",
      "title": "Parcel has been delivered to carrier",
      "event": "delivered_carrier",
      "location": "9370 Hals"
    },
    {
        "timestamp": "2018-01-15 03:00",
      "title": "Arrived to parcel sorting center Aalborg",
      "event": "arrived",      
      "location": "9000 Aalborg"
    },
    {
        "timestamp": "2018-01-15 10:00",
      "title": "Parcel delivered to customer",
      "event": "delivered",            
      "location": "Bøgildsmindevej 3, 9400 Nørresundby"
    }
  ]
}';

$tracking = $api->getShipmentTracking('00057126960221449174');

$html = array();
var_dump($tracking);
foreach ($tracking as $entry) {
    ob_start();
    var_dump($entry);
    ?>
    <div>
        <sub><?php echo $tracking->package_number ?></sub>
        <h3><?php echo $entry->title ?></h3>
    </div>
    <?php

    $html[] = ob_get_clean();
}

echo implode('<hr>', $html);

?>

<?php if (isset($dao)) : ?>
    <h1>List</h1>
    <?php if ($dao) : ?>
        <?php foreach ($dao as $servicepoint) :
            /** @var \CoolRunnerSDK\Models\ServicePoints\Servicepoint $servicepoint */ ?>
            <hr>
            <small><?php echo $servicepoint->id ?></small>
            <h2>
                <?php echo $servicepoint->name ?>
            </h2>
            <small>
                <?php echo $servicepoint->address ?>
            </small>
            <div>
                <dl>
                    <?php echo $servicepoint->opening_hours->toString('<dt class="test">:day: :from - :to</dt>') ?>
                </dl>
                <p>
                    <?php echo $servicepoint->coordinates ?>
                </p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <h1>Single</h1>
    <?php if ($sp) : ?>
        <?php $servicepoint = $sp ?>
        <hr>
        <small><?php echo $servicepoint->id ?></small>
        <h2>
            <?php echo $servicepoint->name ?>
        </h2>
        <small>
            <?php echo $servicepoint->address ?>
        </small>
        <div>
            <dl>
                <?php echo $servicepoint->opening_hours->toString(null, '<dt class="test">:day: :from - :to</dt>') ?>
            </dl>
            <p>
                <?php echo $servicepoint->coordinates ?>
            </p>
        </div>
    <?php endif; ?>
<?php endif; ?>