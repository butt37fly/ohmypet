<?php $products = get_products(); ?>

<?php foreach ($products as $product): ?>

  <article class="Product Product--<?php echo $product->category_id?>">
    <div class="Product__wrapper">
      <section class="Product__side Product__side--front">
        <div class="Product__content">
          <div class="Product__thumb">
            <img src="<?php echo SITE_URI . "public/img/products/$product->thumb" ?>" alt="<?php echo $product->title ?>"
              class="Product__img">
          </div>
          <h2 class="Product__title Product__title--shorted">
            <?php echo $product->title ?>
          </h2>
          <div class="Product__info">
            <span class="Product__price">
              <?php echo "$$product->price/u" ?>
            </span>
            <span class="Product__availability">Disponible (3)</span>
          </div>
        </div>
      </section>
      <section class="Product__side Product__side--back">
        <div class="Prouct__content">
          <h2 class="Product__title">
            <?php echo $product->title ?>
          </h2>
          <div class="Product__info">
            <span class="Product__item">
              <?php echo get_product_icon('pet', $product->pet_id); ?>
              <?php echo $product->pet_name ?>
            </span>
            <span class="Product__item">
              <?php echo get_product_icon('category', $product->category_id, $product->pet_id); ?>
              <?php echo $product->category_name ?>
            </span>
            <span class="Product__item">
              <i class="fa-solid fa-sack-dollar"></i>
              <?php echo "$$product->price/u" ?>
            </span>
            <span class="Product__item">
              <i class="fa-solid fa-bag-shopping"></i>
              <?php echo $product->amount > 0 ? "Disponible ($product->amount)" : "Agotado"; ?>
            </span>
          </div>
          <form class="Add-to-cart" action="<?php echo SITE_URI . "c/cart/" ?>" method="POST">
            <input type="hidden" name="id" value="<?php echo $product->id ?>">
            <label for="amount-<?php echo $product->id ?>">Cantidad</label>
            <div class="Form__field">
              <input type="number" name="amount" id="amount-<?php echo $product->id ?>" min="1" max="4">
              <input type="submit" value="Añadir" class="Button">
            </div>
          </form>
        </div>
      </section>
    </div>
  </article>

<?php endforeach; ?>