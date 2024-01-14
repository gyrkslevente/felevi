<?php
session_start();

//___________________________________________________
if ($_POST['jelnev'] == ""){
    die("Nem adtál nevet!");
}

if ($_POST['jelszul'] == ""){
    die("Nem adtál   születési dátumot!");
}

if ($_POST['jelmail'] == ""){
    die("Nem adtál   emailcímet!");
}

if (!mb_ereg('[a-zA-z-0-9]@gmail.com', $_POST['jelmail']))
{
    die("Az általad megadott email cím nem felel meg !");
}

if ($_POST['jeltel'] == ""){
    die("Nem adtál   telefonszámot!");
}


if (!mb_ereg('[+0-9]', $_POST['jeltel']))
{
    die("Az általad megadott telefon szám nem felel meg a formai követelményeknek!");
}

if ($_POST['jelmunkhely'] == ""){
    die("Nem adtad meg a munkahelyed címét!");
}

if ($_POST['jelmunkor'] == ""){
    die("Nem adtad meg a munkakörödet!");
}

if ($_POST['jelbeoszt'] == ""){
    die("Nem adtad meg a munka beosztásodat!");
}

$kep = $_FILES['jelarckep'];
if ($kep['name'] == ""){
    die("Nem csatoltál fájlt!");
}
//______________________________________________
//robotos_______________________________________________________________
if ($_SESSION['ossz'] != $_POST['captcha'])
{
    die("Nem jó az eredmény!");
}

if ($kep['type'] == "image/webp" && $kep['type'] == "image/web" && $kep['type'] == "image/jpeg")
{
    die("Csak jpg kiterjesztésű képet lehet feltölteni!");
}
list($width, $height) = getimagesize(filesize($kep));
if ( $width < 480 && $height < 480)
{
    die("480X480 pixelnél kisebb képet nem lehet feltölteni.");
}
_________________________________________________________________________________
//kép maximális 640 cm-es jpg fajl

if (imagesx($kep) > 640 && imagesy($kep) > 640){
    $newwidth = 640;
    $newheight = 640;
    $kepnev  = $_POST['jelmail'] . ".jpg" ;
    if ($kepnev == "/jel_mappa/" . $fajlnev)
    {
        $fajlnev = "/jel_mappa/" . $kepnev;
        $ujkep = imagecreatetruecolor($newwidth, $newheight);
        $ujkepmas = imagecopyresampled($newkep, $kep, 0,0,0,0, $newwidth, $newheight, $kep[0], $kep[1]);
        move_uploaded_file($ujkepmas, "/jel_mappa/" . $faljnev);
    }
    else{
        die("Ezzel az email címmel már regisztráltak!");
    }
}else{
    $kepnev  = $_POST['jelmail'] . $kiter ;
    $fajlnev = "/jel_mappa/" . $kepnev;
    move_uploaded_file($kep, "/jel_mappa/" . $fajlnev);
}
//___________________________________________________________________________________________________
//txt fájl 
$beiras = $_POST['jelnev'] .  ";" . $_POST['jelszul'] . ";" . $_POST['jelmail'] . ";" . $_POST['jeltel'] . ";" . $_POST['jelmunkhely'] . ";" . $_POST['jelkor']. ";" . $_POST['jelbeoszt'] . ";" . $kep['name'] . ";" . date("Y.m.d H:i:s"). ";" . $_SERVER['REMOTE_ADDR'];
$szovegesfajl = $_POST['jelmail'] . ".txt";
$fp = fopen($szovegesfajl, "r");
fwrite($fp, $beiras);
fclose($fp);
//_________________________________________________________________________________________________
//létszám  
if (!file_exists("SEO/jel_mappa/" . $szamlalo_fajl))
{
    $szamlalo_fajl = "/SEO/jel_mappa/" . "Számláló" . ".txt";
}

$fp = fopen("SEO/jel_mappa/" . $szamlalo_fajl, "r");
$_SESSION['szamlalo'] = fread($fp, filesize($szamlalo));
fclose($fp);

if (file_exists($szamlalo_fajl) && $_SESSION['szamlalo'] <= 120)
{
$szamlalo++;
$fp = fopen("SEO/jel_mappa/" . $szamlalo_fajl, "w");
fwrite($fp, $_SESSION['szamlalo']);
fclose($fp);
}
else
{
    echo "parent.location.href = ./SEO/ertesit.php";
}
//__________________________________________________________________________
/*Dokumentum beállításai */
date_default_timezone_set("Europe/budapest");
include("./SEO/fpdf17/fpdf.php");

$newpage = new FPDF(); $pdf -> Addpage();   
$pdf ->AddFont('Arial', '', 'arial.php');
$pdf ->AddFont('Arial', 'B', 'arialb.php');
$pdf ->AddFont('Arial', 'BI', 'arialb.php');
$pdf ->AddFont('Arial', 'I', 'arial.php');

$pdf ->SetAutoPageBreak(0);
//_____________________________________________________________________________________
/* Dokumentum fejléce */
$pdf ->SetTextColor(0,0,0);
$pdf ->SETXY(30,20);
$pdf ->SetFont('Arial', 'B', 14);
$pdf ->Write(2, 'SEO kártya');
$pdf ->SetLineWidth(0.5);
$pdf->Line(18,28, 192,28);
//____________________________________________________________________________________________
/* Dokumentum tartalma */
$pdf ->SetTextColor(0,0,0);
$pdf ->SetDrawColor(0,0,0,);
$pdf ->SetLineWidth(0.3);
$pdf ->SetLeftMargin(10);
$pdf ->SetRightMargin(10);
$pdf ->Rect(20, 80, 90, 55, 'D');
$pdf ->Text(21, 83, "SEO konferencia 2024.05.21");
$pdf ->Image($kep, 23, 88, '' , '');
$pdf ->Text(38, 88, "$_POST[jelnev]\n $_POST[jelmunkhely]");
//__________________________________________________________________________________________
/*Dokumentum lábjegyzet  */
$pdf ->SetTextColor(0,0,0);
$pdf ->SETXY(10,200);
$pdf ->SetFont('Arial', 'B', 10);
$pdf ->Write(2, $_SERVER['REMOTE_ADDR']);
//_____________________________________________________________________________________
/*Dokumentum mentése */
$mentés = "/.SEO/jel_mappa/" . $_POST['jelmunkhely'] . ".pdf";
$pdf ->Output($mentés, 'F');
//_______________________________________________________________________________________
// email-en  értesítés
$to = $_Post['jelmail'];
$subject = "Sikeres jelentkezés";
$message = "Köszönjük! \n
            A jelentkezése sikeres\n
            Az alábbi linken tudja letölteni a pdf dokumentumot.\n
            $mentés";
$headers = array(
    'From' => 'seokonference@gmail.com',
    'Reply' => $_POST['jelmail']
);


mail($to, $subject, $message, $headers);

/*Jelenléti dokumentum */
while ($_SESSION['szamlalo'] <= 120)
{
/*Dokumentum beállításai */
date_default_timezone_set("Europe/budapest");
include("./SEO/fpdf17/fpdf.php");
$newpage = new FPDF(); $pdff -> Addpage();
$pdff ->AddFont('Arial', '', 'arial.php');
$pdff ->AddFont('Arial', 'B', 'arialb.php');
$pdff ->AddFont('Arial', 'BI', 'arialb.php');
$pdff ->AddFont('Arial', 'I', 'arial.php');

$pdff ->SetAutoPageBreak(0);

/* Dokumentum tartalma */
$pdff ->SetTextColor(0,0,0);
$pdff ->SetDrawColor(0,0,0,);
$pdff ->SetLineWidth(0.3);
$pdff ->SetLeftMargin(10);
$pdff ->SetRightMargin(10);

for ($i = 0; $i < 120; $i++){
$pdff ->SETXY(40, 65+$i*5);
$pdff ->Cell(378, 189, "$szamlalo", 1);
$pdff ->Cell(378, 189, "$_POST[jelnev]", 1);
$pdff ->Cell(378, 189, "", 1);
}

/*Dokumentum mentése */
$mentes = "/.SEO/jel_mappa/" . "Jelenléti ív" . ".pdf";
$pdff ->Output($mentes, 'F');
}

print "
<script>
    alert('Köszönjük regisztrációdat!')
    parent.location.href='parent.location.href'
</script>
";
?>
