/*=========================================================================================
    File Name: popover.js
    Description: Popovers are an updated version, which don’t rely on images, use CSS3 for animations, and data-attributes for local title storage.
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/
(function (window, document, $) {
  'use strict';

  try {
  // Basic Initialization
  var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));

  var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
    return new bootstrap.Popover(popoverTriggerEl);
  });

  /******************/
  // Popover events //
  /******************/

  // onShow event
  var showPopoverTrigger = document.getElementById('show-popover');
  if (showPopoverTrigger) {
  var showPopover = new bootstrap.Popover(showPopoverTrigger, {
    title: 'Popover Show Event',
    content: 'Bonbon chocolate cake. Pudding halvah pie apple pie topping marzipan pastry marzipan cupcake.',
    trigger: 'click',
    placement: 'right'
  });

  showPopoverTrigger.addEventListener('show.bs.popover', function () {
    alert('Show event fired.');
  });
  }

  // onShown event
  var shownPopoverTrigger = document.getElementById('shown-popover');
  if (shownPopoverTrigger) {
  var shownPopover = new bootstrap.Popover(shownPopoverTrigger, {
    title: 'Popover Shown Event',
    content: 'Bonbon chocolate cake. Pudding halvah pie apple pie topping marzipan pastry marzipan cupcake.',
    trigger: 'click',
    placement: 'bottom'
  });

  shownPopoverTrigger.addEventListener('shown.bs.popover', function () {
    alert('Shown event fired.');
  });
  }

  // onHide event
  var hidePopoverTrigger = document.getElementById('hide-popover');
  if (hidePopoverTrigger) {
  var hidePopover = new bootstrap.Popover(hidePopoverTrigger, {
    title: 'Popover Hide Event',
    content: 'Bonbon chocolate cake. Pudding halvah pie apple pie topping marzipan pastry marzipan cupcake.',
    trigger: 'click',
    placement: 'bottom'
  });

  hidePopoverTrigger.addEventListener('hide.bs.popover', function () {
    alert('Hide event fired.');
  });
  }

  // onHidden event
  var hiddenPopoverTrigger = document.getElementById('hidden-popover');
  if (hiddenPopoverTrigger) {
  var hiddenPopover = new bootstrap.Popover(hiddenPopoverTrigger, {
    title: 'Popover Hidden Event',
    content: 'Bonbon chocolate cake. Pudding halvah pie apple pie topping marzipan pastry marzipan cupcake.',
    trigger: 'click',
    placement: 'left'
  });

  hiddenPopoverTrigger.addEventListener('hidden.bs.popover', function () {
    alert('Hidden event fired.');
  });
  }

  // onInserted event
  var insertedPopoverTrigger = document.getElementById('inserted-popover');
  if (insertedPopoverTrigger) {
  var insertedPopover = new bootstrap.Popover(insertedPopoverTrigger, {
    title: 'Popover Inserted Event',
    content: 'Bonbon chocolate cake. Pudding halvah pie apple pie topping marzipan pastry marzipan cupcake.',
    trigger: 'click',
    placement: 'left'
  });

  insertedPopoverTrigger.addEventListener('inserted.bs.popover', function () {
    alert('Inserted event fired.');
  });
  }

  /*******************/
  // Tooltip methods //
  /*******************/

  // Show method
  var showMethod = document.getElementById('show-method');
  if (showMethod) {
  var showPopoverMethod = new bootstrap.Popover(showMethod);
  showMethod.addEventListener('click', function () {
    showPopoverMethod.show();
  });
  }

  // Hide method
  var hideMethod = document.getElementById('hide-method');
  if (hideMethod) {
  var hidePopoverMethod = new bootstrap.Popover(hideMethod);
  hideMethod.addEventListener('mouseenter', function () {
    hidePopoverMethod.show();
  });

  hideMethod.addEventListener('click', function () {
    hidePopoverMethod.hide();
  });
  }

  // Toggle method
  var toggleMethod = document.getElementById('toggle-method');
  if (toggleMethod) {
  var togglePopoverMethod = new bootstrap.Popover(toggleMethod);
  toggleMethod.addEventListener('click', function () {
    togglePopoverMethod.toggle();
  });
  }

  /* Manual Trigger*/
  var popoverTriggerListMn = document.getElementById('manual-popover');
  if (popoverTriggerListMn) {
  var manualPopover = new bootstrap.Popover(popoverTriggerListMn);
  popoverTriggerListMn.addEventListener('click', function () {
    manualPopover.toggle();
  });
  }

  } catch(e) {
    console.warn('Popover initialization skipped:', e.message);
  }
})(window, document, jQuery);
