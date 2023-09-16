<?php $pets = get_pets();
$categories = get_categories(); ?>

<div class="Modal" data-modal-name="Products">
  <div class="Modal__wrapper">
    <i class="fa fa-circle-xmark Modal__button" data-action="Close" data-target="Products"></i>
    <section class="Modal__header">
      <h2>Añade un nuevo producto</h2>
    </section>
    <form action="<?php echo SITE_URI . "c/products/" ?>" class="Form" method="POST" enctype="multipart/form-data">
      <div class="Form__section">
        <div class="Form__field">
          <label for="title">Nombre</label>
          <input class="Form__input" id="title" type="text" name="title" required placeholder="Shampoo para perros">
        </div>
        <div class="Form__field">
          <label for="price">Precio por unidad</label>
          <input class="Form__input" id="price" type="number" name="price" required placeholder="10000">
        </div>
      </div>
      <div class="Form__section">
        <div class="Form__field">
          <label for="thumb">Imágen</label>
          <input class="Form__input" id="thumb" type="file" name="thumb">
        </div>
      </div>
      <div class="Form__section">
        <div class="Form__field">
          <label for="pet">Mascota</label>
          <select class="Form__input" name="pet" id="pet">

            <?php foreach ($pets as $pet): ?>

              <option value="<?php echo $pet->id ?>">
                <?php echo $pet->name ?>
              </option>

            <?php endforeach; ?>

          </select>
        </div>
        <div class="Form__field">
          <label for="category">Categoría</label>
          <select class="Form__input" name="category" id="category">

            <?php foreach ($categories as $category): ?>

              <option value="<?php echo $category->id ?>">
                <?php echo $category->name ?>
              </option>

            <?php endforeach; ?>

          </select>
        </div>
      </div>
      <div class="Form__section">
        <input type="hidden" name="id" id="id">
        <input type="submit" class="Button" name="submit" id="submit" value="Guardar">
      </div>
    </form>
  </div>
</div>