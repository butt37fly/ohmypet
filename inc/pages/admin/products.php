<?php $products = get_products(); ?>

<div class="Products">

  <?php if (empty($products)): ?>

    <h1 class="Products__title"> Parece que no hay productos registrados.</h1>

  <?php else: ?>

    <ul class="List">

      <?php
      foreach ($products as $product):
        $attr = '{"id":'.$product->id.',"title":"'.$product->title.'","price":'.$product->price.',"pet":'.$product->pet_id.',"category":'.$product->category_id.'}' ?>

        <li class="List__item">
          <span class="List__text">
            <?php echo $product->id; ?>
          </span>
          <span class="List__img">
            <img src="<?php echo SITE_URI . "public/img/products/$product->thumb"; ?>">
          </span>
          <span class="List__text">
            <?php echo $product->title; ?>
          </span>
          <span class="List__text">
            <?php echo "$ $product->price"; ?>
          </span>
          <span class="List__text">
            <?php echo "$product->pet_name"; ?>
          </span>
          <span class="List__text">
            <?php echo "$product->category_name"; ?>
          </span>
          <div class="List__actions">
            <div class="List__edit" data-item="<?php echo $product->id; ?>">
              <i class="fa fa-pen" data-action="Edit" data-target="Products">
                <span style="display:none"> <?php echo $attr?> </span>
              </i>
            </div>
            <a href="<?php echo SITE_URI . "c/products/delete/$product->id/"; ?>" class="List__delete">
              <i class="fa fa-trash"></i>
            </a>
          </div>
        </li>

      <?php endforeach; ?>

    </ul>

  <?php endif; ?>

</div>
<div class="Shortcut">
  <i class="fa-solid fa-circle-plus Shortcut__button" data-action="Open" data-target="Products"></i>
</div>

<?php require_once "inc/parts/modal/products.php" ?>