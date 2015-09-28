<?

require_once( $_SERVER['DOCUMENT_ROOT'] . '/../base.php');

$user = DB_Peak_Users::fetch_by_id(1);

$rand = rand(1000000000, 4294967295);

var_dump($rand);

$user->phones[0]->phone = $rand;

$user->save();

var_dump(DB_Peak_Users::fetch_by_id(1)->phones[0]->phone);



?>

<html>
    <body>

    <? include_once($_SERVER['DOCUMENT_ROOT'] . "/scripts/generate_db_model.php"); ?>

    </body>
</html>

<?

include_once($_SERVER['DOCUMENT_ROOT'] . '/footer.php');
