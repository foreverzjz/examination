<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
 <meta content="" name="description"/>
 <meta content="" name="author"/>
 <title>学生在线考试系统</title>
 <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css?v4.0.0">
 <link rel="stylesheet" href="/css/shards.css?v2.0.1">
 <link rel="stylesheet" href="/css/font-awesome.min.css?v4.7.0.2">
 <script src="/js/jquery-3.3.1.min.js"></script>
 <script>
  if (window.top !== window.self) {
   window.top.location = window.location;
  }
 </script>
</head>
<body>
 欢迎来到王者荣耀！<button type="button" class="btn bth-primary go">远离王者荣耀的世界</button>
</body>
<script>
 $(".go").click(function () {
    $.post("<?= $this->url->get('logout') ?>",{},function(){
       self.location.reload();
    })
 });
</script>