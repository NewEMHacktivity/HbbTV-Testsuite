<?php
$ROOTDIR='..';
require("$ROOTDIR/base.php");
sendContentType();
openDocument();

?>
<script type="text/javascript">
//<![CDATA[
var eventnames = ['stopped', 'playing', 'paused', 'connecting', 'buffering', 'finished', 'error'];
var foundevents = [];
var starttime = 0;
var eventtxt = '';
var speedchangereceived = false;
var poschangereceived = false;
var pausetimer = false;

window.onload = function() {
  menuInit();
  registerKeyEventListener();
  initApp();
  setInstr('Please run all steps in the displayed order. Navigate to the test using up/down, then press OK to start the test. For some tests, you may need to follow some instructions.<br /><br /><b>IMPORTANT: The test result is displayed when the video stops. If no result is displayed at the end of the video, a required finished/error event is not sent. Please check if the video starts playing as soon as the playing event is received.<'+'/b>');
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
  setInstr('Starting video...');
  showStatus(true, '');
  var vid = document.getElementById('video');
  vid.onPlayStateChange = null;
  vid.onPlaySpeedChanged = null;
  vid.onPlayPositionChanged = null;
  if (pausetimer) {
    clearTimeout(pausetimer);
  }
  try {
    vid.stop();
  } catch (e) {
    // ignore
  }
  for (var i=0; i<eventnames.length; i++) {
    foundevents[i] = 0;
  }
  speedchangereceived = false;
  poschangereceived = false;
  document.getElementById('vidstate').innerHTML = 'Waiting for first onPlayPositionChanged event...';
  vid.onPlayStateChange = function() {
    var state = vid.playState;
    var ename = 'unknown event state '+state;
    if (state>=0 || state<eventnames.length) {
      foundevents[state]++;
      ename = eventnames[state]+'('+state+')';
    }
    eventtxt += '<br />@sec '+Math.floor((new Date().getTime()-starttime)/1000)+': '+ename;
    setInstr('Waiting while playing video (test result is displayed at end of video...'+eventtxt);
    if (state==5 || state==6) {
      showResult(name);
    }
  };
  vid.onPlaySpeedChanged = function() {
    speedchangereceived = true;
  };
  vid.onPlayPositionChanged = function() {
    poschangereceived = true;
    document.getElementById('vidstate').innerHTML = 'Play position = '+vid.playPosition+'<br />Play time = '+vid.playTime;
  };
  starttime = new Date().getTime();
  if (name=='valid') {
    vid.data = 'http://itv.ard.de/video/trailer.php';
    vid.play(1);
    checkpausetimer(); // pause video and restart it in order to check if events are sent correctly
  } else if (name=='invalid') {
    vid.data = 'http://itv.mit-xperts.com/hbbtvtest/playerevents/novideo.mp4';
    vid.play(1);
  }
}
function checkpausetimer() {
  var vid = document.getElementById('video');
  var state = (vid && vid.playState) ? vid.playState : 0;
  if (state==3 || state==4) { // still connecting or buffering
    pausetimer = setTimeout(function() { checkpausetimer(); }, 2000);
  } else {
    pausetimer = setTimeout(function() {
      vid.play(0);
      pausetimer = setTimeout(function() {
        vid.play(1);
        pausetimer = false;
      }, 5000);
    }, 12000);
  }
}
function showResult(name) {
  var errmsg = '';
  if (name=='valid') {
    if (foundevents[1]!=2) {
      errmsg += '<br />not 2x PLAYING events received ('+foundevents[1]+'x instead)';
    }
    if (foundevents[2]!=1) {
      errmsg += '<br />not 1x PAUSED event received ('+foundevents[2]+'x instead)';
    }
    if (!foundevents[3]) {
      errmsg += '<br />no CONNECTING event received';
    }
    if (foundevents[5]>1) {
      errmsg += '<br />multiple FINISHED events received';
    }
    if (!foundevents[5]) {
      errmsg += '<br />no FINISHED event received';
    }
    if (foundevents[6]) {
      errmsg += '<br />ERROR event received';
    }
    if (!speedchangereceived) {
      errmsg += '<br />no onPlaySpeedChanged event received';
    }
    if (!poschangereceived) {
      errmsg += '<br />no onPlayPositionChanged event received';
    }
  } else if (name=='invalid') {
    if (foundevents[1]) {
      errmsg += '<br />PLAYING event received';
    }
    if (foundevents[5]) {
      errmsg += '<br />FINISHED event received';
    }
    if (foundevents[6]>1) {
      errmsg += '<br />multiple ERROR events received';
    }
    if (!foundevents[6]) {
      errmsg += '<br />no ERROR event received';
    }
  }
  if (errmsg) {
    showStatus(false, 'The received events were not correct.');
    setInstr('The following problems were detected:'+errmsg);
  } else {
    showStatus(true, 'Test succeeded.');
  }
}

//]]>
</script>

</head><body>

<div style="left: 0px; top: 0px; width: 1280px; height: 720px; background-color: #132d48;" />

<object id="video" type="video/mp4" style="position: absolute; left: 100px; top: 480px; width: 320px; height: 180px;"></object>
<?php echo appmgrObject(); ?>

<div class="txtdiv txtlg" style="left: 110px; top: 60px; width: 500px; height: 30px;">MIT-xperts HBBTV tests</div>
<div id="instr" class="txtdiv" style="left: 700px; top: 110px; width: 400px; height: 360px;"></div>
<div id="vidstate" class="txtdiv" style="left: 700px; top: 420px; width: 400px; height: 60px;"></div>
<ul id="menu" class="menu" style="left: 100px; top: 100px;">
  <li name="valid">Test 1: play valid video</li>
  <li name="invalid">Test 2: play invalid video</li>
  <li name="exit">Return to test menu</li>
</ul>
<div id="status" style="left: 700px; top: 480px; width: 400px; height: 200px;"></div>

</body>
</html>