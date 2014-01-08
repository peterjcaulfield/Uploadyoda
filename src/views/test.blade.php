<html>
<head>
</head>
<body>
<style>
@import url(http://reset5.googlecode.com/hg/reset.min.css);
html { padding-top: 32px; font-family: verdana; font-size: 14px;}
body { background-color: #f1f1f1; }
#nav-back { position: fixed; top: 0; left: 0; width: 160px; background: #222; height: 100%; }
#nav { position: fixed; width: 160px; background: #222;  color: #eee; }
#content { margin-left: 180px; height: 100%; }
#header { height: 32px; position: fixed; top: 0; left: 0; width: 100%; min-width: 600px; z-index: 99999; background: #222; }
#body { position: relative;  padding: 0 25px 65px 0; overflow: auto; min-width: 900px; }
#body-content { padding-bottom: 65px; width: 100%; overflow: visible!important; background-color:#fff; }
.wrap { margin: 10px 20px 0 2px; }
#dummy { height: 1000px; }
</style>
<div id="nav-back"></div>
<div id="nav">Nav here</div>
<div id="content">
    <div id="header"></div>
    <div id="body">
        <div id="body-content">
            <div class="wrap">
                <div id="dummy">content here</div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
