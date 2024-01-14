<?php
session_start();
$szamok = array("", "egy", "kettő", "három", "négy", "öt", "hat", "hét", "nyolc", "kilenc");
$szam1 = rand(1, 9);
$szam2 = rand(2, 9);
$_SESSION['ossz'] = $szam1+$szam2;

?>
<style>
*{
    font-family: Arial, Helvetica, sans-serif;
}
h1{
    text-align: center;
    color: blue;
}
form{
    background-color: grey;
    border: 1px solid black;
    padding-left: 120px;
    margin-left: 500px;
    margin-right: 500px;
}
#gomb{
    margin-bottom: 10px;
}
</style>


<h1>SEO-konferencia</h1>
<form action='SEO_action.php' method='POST' enctype='multipart/form-data'>
<p>Neve:</p>
<input type='text' name='jelnev'>
<p>Születési dátuma:</p>
<input type='date' name='jelszul'>
<p>Email-címe:</p>
<input type='email' name='jelmail'>
<p>Telefon:</p>
<input type='tel' name='jeltel'>
<p>Munkahelyének neve:</p>
<input type='text' name='jelmunkhely'>
<p>Munkahelyének címe:</p>
<input type='text' name='jelmunkcim'>
<p>Munkaköre:</p>
<input type='text' name='jelmunkor'>
<p>Munkabeli beosztása:</p>
<input type='text' name='jelbeoszt'>
<p>Jelentkező arcképe:</p>
<input type='file' name='jelarckep'>
<p>Add össze a <?=$szamok[$szam1];?> + <?=$szamok[$szam2];?> ?<br>
<input type='text' name='captcha'></p>
<input type="submit" value="Regisztrálás" id="gomb">
</form>