<nav class="navbar navbar-default navbar-menu">
  <ul class="nav-atc-list">
    <?php foreach ($items as $name=>$params):?>
    <li>
      <a href="<?= $params['url'];?>">
        <p class="menu-title menu-icon <?= $params['class'];?>"><?= $name;?></p>
        <p class="menu-describe"><?= $params['describe'];?></p>
      </a>
    </li>
    <?php endforeach;?>                
  </ul>
</nav>
