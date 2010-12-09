<?php
$ROOTDIR='..';
require("$ROOTDIR/base.php");
sendContentType();
openDocument();

?>
<script type="text/javascript">
//<![CDATA[
window.onload = function() {
  menuInit();
  registerKeyEventListener();
  initApp();
};
function handleKeyCode(kc) {
  if (kc==VK_UP) {
    menuSelect(selected-1);
    return true;
  } else if (kc==VK_DOWN) {
    menuSelect(selected+1);
    return true;
  } else if (kc==VK_ENTER) {
    var liid = opts[selected].getAttribute('name');
    if (liid=='exit') {
      document.location.href = '../index.php';
    } else {
      runStep(liid);
    }
    return true;
  }
  return false;
}
function runStep(name) {
}

//]]>
</script>

</head><body>

<div style="left: 0px; top: 0px; width: 1280px; height: 720px; background-color: #132d48;" />

<?php echo appmgrObject(); ?>

<div class="txtdiv txtlg" style="left: 110px; top: 60px; width: 500px; height: 30px;">MIT-xperts HBBTV tests</div>
<div class="txtdiv" style="left: 110px; top: 150px; width: 400px; height: 550px;"><u>Contact / Imprint:</u><br />
MIT-xperts GmbH<br />
Poccistr. 13<br />
80336 Munich, Germany<br />
info &#x40; mit-xperts&#x2e;com<br />
Phone: +49 89 76756380<br /><br />
<u>Contributing:</u><br />
In case you have an additional test(s), we would be very happy to include your test in this testsuite. Please contact us (see above) to discuss further details.<br /><br />
</div>

<div class="txtdiv" style="left: 700px; top: 110px; width: 400px; height: 600px;"><u>About this testsuite:</u><br />
This test suite is for HbbTV terminal developers to test their implementation of the HbbTV 1.1.1 standard. Although this test suite contains a lot of test, it is not complete. It contains the most important interoperability issues disvocered in current applications. Tested parts are not covered by 100%, but the most importent checks are performed. Identified parts of the specification that are not covered so far are:<ul>
<li>application/oipfDrmAgent (HbbTV 8.2.3)</li>
<li>Parental access (OIPF DAE 4.5)</li>
<li>Configuration class (OIPF DAE 7.3.2)</li>
<li>AV components in video/broadcast object (OIPF DAE 7.13.4)</li>
<li>JavaScript tests</li>
<li>CSS navigation</li>
</ul>
</div>

<ul id="menu" class="menu" style="left: 100px; top: 100px;">
  <li name="exit">Return to test menu</li>
</ul>

</body>
</html>