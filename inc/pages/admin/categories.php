<?php $categories = get_categories(); ?>

<div class="Categories">

  <?php if (empty($categories)): ?>

    <h1 class="Categories__title"> Parece que no hay categorías registradas.</h1>

  <?php else: ?>

    <ul class="List">

      <?php foreach ($categories as $category): ?>

        <li class="List__item">
          <span class="List__text"> <?php echo $category->id; ?> </span>
          <span class="List__text"> <?php echo $category->name; ?> </span>
          <span class="List__text"> <?php echo $category->slug; ?> </span>
          <div class="List__actions">
            <div class="List__edit" data-item="<?php echo $category->id; ?>">
              <i class="fa fa-pen"></i>
            </div>
            <a href="<?php echo SITE_URI . "c/categories/delete/$category->id/"; ?>" class="List__delete">
              <i class="fa fa-trash"></i>
            </a>
          </div>
        </li>

      <?php endforeach; ?>

    </ul>

  <?php endif; ?>

</div>
<div class="Shortcut">

</div>
<div class="Modal">

</div>