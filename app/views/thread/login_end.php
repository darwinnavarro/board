<h2>WELCOME <?php eh($_SESSION['username']) ?></h2>

<p class="alert alert-success">
LOGIN SUCCESSFUL!
</p>

<a href="<?php eh(url('thread/home', array('page'=>1))) ?>">
&larr; Go to THREAD
</a>

