<style>
  /* CSS for course description content - preserves list styles and spacing */
  .course-description .content-area {
    line-height: 1.6;
  }

  /* Styles for unordered lists */
  .course-description .content-area ul {
    list-style-type: disc !important;
    margin: 16px 0 !important;
    padding-left: 30px !important;
  }

  .course-description .content-area ul li {
    list-style-type: disc !important;
    margin-bottom: 8px !important;
    display: list-item !important;
  }

  /* Styles for ordered lists */
  .course-description .content-area ol {
    list-style-type: decimal !important;
    margin: 16px 0 !important;
    padding-left: 30px !important;
  }

  .course-description .content-area ol li {
    list-style-type: decimal !important;
    margin-bottom: 8px !important;
    display: list-item !important;
  }

  /* Styles for paragraphs */
  .course-description .content-area p {
    margin: 16px 0 !important;
    line-height: 1.6 !important;
  }

  /* Styles for headings in content */
  .course-description .content-area h1,
  .course-description .content-area h2,
  .course-description .content-area h3,
  .course-description .content-area h4,
  .course-description .content-area h5,
  .course-description .content-area h6 {
    margin: 24px 0 16px 0 !important;
    font-weight: bold !important;
  }

  .course-description .content-area h1 {
    font-size: 28px !important;
  }

  .course-description .content-area h2 {
    font-size: 24px !important;
  }

  .course-description .content-area h3 {
    font-size: 20px !important;
  }

  /* Styles for bold text */
  .course-description .content-area strong,
  .course-description .content-area b {
    font-weight: bold !important;
  }

  /* Styles for italic text */
  .course-description .content-area em,
  .course-description .content-area i {
    font-style: italic !important;
  }

  /* Styles for links */
  .course-description .content-area a {
    color: #7c3aed !important;
    text-decoration: underline !important;
  }

  .course-description .content-area a:hover {
    color: #5b21b6 !important;
  }

  /* Reset for elements that might be affected by template CSS */
  .course-description .content-area * {
    box-sizing: border-box;
  }
</style>

<div class="pt-60 lg:pt-40 to-over course-description">
  <h4 class="text-18 fw-500">{{ __('messages.course_information') }}</h4>

  <div class="mt-30 content-area">
    @if($product->current_variation && $product->current_variation->description)
      <div>
        {!! $product->current_variation->description !!}
      </div>
    @elseif($product->current_variation && $product->current_variation->short_description)
      <div>
        {!! $product->current_variation->short_description !!}
      </div>
    @elseif($product->description_short)
      <div>
        {!! $product->description_short !!}
      </div>
    @else
      <p>
        {{ $product->description }}
      </p>
    @endif
  </div>
</div>
