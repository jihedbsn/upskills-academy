<section class="courses-section layout-pt-lg layout-pb-lg">
  <div class="container">
    <div class="row justify-center text-center">
      <div class="col-lg-8">
        <div class="sectionTitle">
          <h2 class="sectionTitle__title">Nos cours les plus populaires</h2>
          <p class="sectionTitle__text">
            Découvrez nos formations les plus demandées en intelligence artificielle et technologies émergentes
          </p>
        </div>
      </div>
    </div>

    <div class="tabs -pills pt-50 js-tabs">
      <div class="tabs__controls d-flex justify-center x-gap-10 js-tabs-controls">
        <div>
          <button class="tabs__button px-15 py-8 rounded-8 js-tabs-button is-active" data-tab-target=".-tab-item-1" type="button">Toutes les catégories</button>
        </div>
        <div>
          <button class="tabs__button px-15 py-8 rounded-8 js-tabs-button" data-tab-target=".-tab-item-2" type="button">Machine Learning</button>
        </div>
        <div>
          <button class="tabs__button px-15 py-8 rounded-8 js-tabs-button" data-tab-target=".-tab-item-3" type="button">Deep Learning</button>
        </div>
        <div>
          <button class="tabs__button px-15 py-8 rounded-8 js-tabs-button" data-tab-target=".-tab-item-4" type="button">Data Science</button>
        </div>
        <div>
          <button class="tabs__button px-15 py-8 rounded-8 js-tabs-button" data-tab-target=".-tab-item-5" type="button">Python</button>
        </div>
      </div>

      <div class="tabs__content pt-60 js-tabs-content">
        <div class="tabs__pane -tab-item-1 is-active">
          <div class="row y-gap-30 justify-center">
            <!-- Cours 1 -->
            <div class="col-lg-3 col-md-6">
              <div class="coursesCard -type-1">
                <div class="relative">
                  <div class="coursesCard__image overflow-hidden rounded-8">
                    <img class="w-1/1" src="{{ asset('assets/img/coursesCards/1.png') }}" alt="Machine Learning">
                    <div class="coursesCard__image_overlay rounded-8"></div>
                  </div>
                  <div class="d-flex justify-between py-10 px-10 absolute-full-center z-3">
                    <div>
                      <div class="px-15 rounded-200 bg-purple-1">
                        <span class="text-11 lh-1 uppercase fw-500 text-white">Populaire</span>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="h-100 pt-15">
                  <div class="d-flex items-center">
                    <div class="text-14 lh-1 text-yellow-1 mr-10">4.8</div>
                    <div class="d-flex x-gap-5 items-center">
                      <div class="icon-star text-9 text-yellow-1"></div>
                      <div class="icon-star text-9 text-yellow-1"></div>
                      <div class="icon-star text-9 text-yellow-1"></div>
                      <div class="icon-star text-9 text-yellow-1"></div>
                      <div class="icon-star text-9 text-yellow-1"></div>
                    </div>
                    <div class="text-13 lh-1 ml-10">(2,451)</div>
                  </div>

                  <div class="text-17 lh-15 fw-500 text-dark-1 mt-10">Introduction au Machine Learning</div>

                  <div class="d-flex x-gap-10 items-center pt-10">
                    <div class="d-flex items-center">
                      <div class="mr-8">
                        <img src="{{ asset('assets/img/coursesCards/icons/1.svg') }}" alt="icon">
                      </div>
                      <div class="text-14 lh-1">12 leçons</div>
                    </div>
                    <div class="d-flex items-center">
                      <div class="mr-8">
                        <img src="{{ asset('assets/img/coursesCards/icons/2.svg') }}" alt="icon">
                      </div>
                      <div class="text-14 lh-1">8h 30m</div>
                    </div>
                    <div class="d-flex items-center">
                      <div class="mr-8">
                        <img src="{{ asset('assets/img/coursesCards/icons/3.svg') }}" alt="icon">
                      </div>
                      <div class="text-14 lh-1">Débutant</div>
                    </div>
                  </div>

                  <div class="coursesCard-footer">
                    <div class="coursesCard-footer__author">
                      <img src="{{ asset('assets/img/general/avatar-1.png') }}" alt="image">
                      <div>Dr. Sarah Johnson</div>
                    </div>
                    <div class="coursesCard-footer__price">
                      <div>$199</div>
                      <div>$99</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Cours 2 -->
            <div class="col-lg-3 col-md-6">
              <div class="coursesCard -type-1">
                <div class="relative">
                  <div class="coursesCard__image overflow-hidden rounded-8">
                    <img class="w-1/1" src="{{ asset('assets/img/coursesCards/2.png') }}" alt="Deep Learning">
                    <div class="coursesCard__image_overlay rounded-8"></div>
                  </div>
                  <div class="d-flex justify-between py-10 px-10 absolute-full-center z-3">
                    <div>
                      <div class="px-15 rounded-200 bg-green-1">
                        <span class="text-11 lh-1 uppercase fw-500 text-dark-1">Meilleur vendeur</span>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="h-100 pt-15">
                  <div class="d-flex items-center">
                    <div class="text-14 lh-1 text-yellow-1 mr-10">4.9</div>
                    <div class="d-flex x-gap-5 items-center">
                      <div class="icon-star text-9 text-yellow-1"></div>
                      <div class="icon-star text-9 text-yellow-1"></div>
                      <div class="icon-star text-9 text-yellow-1"></div>
                      <div class="icon-star text-9 text-yellow-1"></div>
                      <div class="icon-star text-9 text-yellow-1"></div>
                    </div>
                    <div class="text-13 lh-1 ml-10">(1,892)</div>
                  </div>

                  <div class="text-17 lh-15 fw-500 text-dark-1 mt-10">Deep Learning avec TensorFlow</div>

                  <div class="d-flex x-gap-10 items-center pt-10">
                    <div class="d-flex items-center">
                      <div class="mr-8">
                        <img src="{{ asset('assets/img/coursesCards/icons/1.svg') }}" alt="icon">
                      </div>
                      <div class="text-14 lh-1">18 leçons</div>
                    </div>
                    <div class="d-flex items-center">
                      <div class="mr-8">
                        <img src="{{ asset('assets/img/coursesCards/icons/2.svg') }}" alt="icon">
                      </div>
                      <div class="text-14 lh-1">12h 45m</div>
                    </div>
                    <div class="d-flex items-center">
                      <div class="mr-8">
                        <img src="{{ asset('assets/img/coursesCards/icons/3.svg') }}" alt="icon">
                      </div>
                      <div class="text-14 lh-1">Intermédiaire</div>
                    </div>
                  </div>

                  <div class="coursesCard-footer">
                    <div class="coursesCard-footer__author">
                      <img src="{{ asset('assets/img/general/avatar-1.png') }}" alt="image">
                      <div>Prof. Michael Chen</div>
                    </div>
                    <div class="coursesCard-footer__price">
                      <div>$299</div>
                      <div>$149</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Cours 3 -->
            <div class="col-lg-3 col-md-6">
              <div class="coursesCard -type-1">
                <div class="relative">
                  <div class="coursesCard__image overflow-hidden rounded-8">
                    <img class="w-1/1" src="{{ asset('assets/img/coursesCards/3.png') }}" alt="Data Science">
                    <div class="coursesCard__image_overlay rounded-8"></div>
                  </div>
                </div>

                <div class="h-100 pt-15">
                  <div class="d-flex items-center">
                    <div class="text-14 lh-1 text-yellow-1 mr-10">4.7</div>
                    <div class="d-flex x-gap-5 items-center">
                      <div class="icon-star text-9 text-yellow-1"></div>
                      <div class="icon-star text-9 text-yellow-1"></div>
                      <div class="icon-star text-9 text-yellow-1"></div>
                      <div class="icon-star text-9 text-yellow-1"></div>
                      <div class="icon-star text-9 text-yellow-1"></div>
                    </div>
                    <div class="text-13 lh-1 ml-10">(1,234)</div>
                  </div>

                  <div class="text-17 lh-15 fw-500 text-dark-1 mt-10">Data Science pour débutants</div>

                  <div class="d-flex x-gap-10 items-center pt-10">
                    <div class="d-flex items-center">
                      <div class="mr-8">
                        <img src="{{ asset('assets/img/coursesCards/icons/1.svg') }}" alt="icon">
                      </div>
                      <div class="text-14 lh-1">15 leçons</div>
                    </div>
                    <div class="d-flex items-center">
                      <div class="mr-8">
                        <img src="{{ asset('assets/img/coursesCards/icons/2.svg') }}" alt="icon">
                      </div>
                      <div class="text-14 lh-1">10h 20m</div>
                    </div>
                    <div class="d-flex items-center">
                      <div class="mr-8">
                        <img src="{{ asset('assets/img/coursesCards/icons/3.svg') }}" alt="icon">
                      </div>
                      <div class="text-14 lh-1">Débutant</div>
                    </div>
                  </div>

                  <div class="coursesCard-footer">
                    <div class="coursesCard-footer__author">
                      <img src="{{ asset('assets/img/general/avatar-1.png') }}" alt="image">
                      <div>Dr. Emma Wilson</div>
                    </div>
                    <div class="coursesCard-footer__price">
                      <div>$179</div>
                      <div>$89</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Cours 4 -->
            <div class="col-lg-3 col-md-6">
              <div class="coursesCard -type-1">
                <div class="relative">
                  <div class="coursesCard__image overflow-hidden rounded-8">
                    <img class="w-1/1" src="{{ asset('assets/img/coursesCards/4.png') }}" alt="Python AI">
                    <div class="coursesCard__image_overlay rounded-8"></div>
                  </div>
                </div>

                <div class="h-100 pt-15">
                  <div class="d-flex items-center">
                    <div class="text-14 lh-1 text-yellow-1 mr-10">4.6</div>
                    <div class="d-flex x-gap-5 items-center">
                      <div class="icon-star text-9 text-yellow-1"></div>
                      <div class="icon-star text-9 text-yellow-1"></div>
                      <div class="icon-star text-9 text-yellow-1"></div>
                      <div class="icon-star text-9 text-yellow-1"></div>
                      <div class="icon-star text-9 text-yellow-1"></div>
                    </div>
                    <div class="text-13 lh-1 ml-10">(987)</div>
                  </div>

                  <div class="text-17 lh-15 fw-500 text-dark-1 mt-10">Python pour l'IA</div>

                  <div class="d-flex x-gap-10 items-center pt-10">
                    <div class="d-flex items-center">
                      <div class="mr-8">
                        <img src="{{ asset('assets/img/coursesCards/icons/1.svg') }}" alt="icon">
                      </div>
                      <div class="text-14 lh-1">20 leçons</div>
                    </div>
                    <div class="d-flex items-center">
                      <div class="mr-8">
                        <img src="{{ asset('assets/img/coursesCards/icons/2.svg') }}" alt="icon">
                      </div>
                      <div class="text-14 lh-1">15h 30m</div>
                    </div>
                    <div class="d-flex items-center">
                      <div class="mr-8">
                        <img src="{{ asset('assets/img/coursesCards/icons/3.svg') }}" alt="icon">
                      </div>
                      <div class="text-14 lh-1">Tous niveaux</div>
                    </div>
                  </div>

                  <div class="coursesCard-footer">
                    <div class="coursesCard-footer__author">
                      <img src="{{ asset('assets/img/general/avatar-1.png') }}" alt="image">
                      <div>Alex Rodriguez</div>
                    </div>
                    <div class="coursesCard-footer__price">
                      <div>$249</div>
                      <div>$129</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Autres onglets pour les catégories -->
        <div class="tabs__pane -tab-item-2">
          <div class="row y-gap-30 justify-center">
            <!-- Contenu pour Machine Learning -->
            <div class="col-12 text-center">
              <p>Contenu spécifique aux cours de Machine Learning</p>
            </div>
          </div>
        </div>

        <div class="tabs__pane -tab-item-3">
          <div class="row y-gap-30 justify-center">
            <!-- Contenu pour Deep Learning -->
            <div class="col-12 text-center">
              <p>Contenu spécifique aux cours de Deep Learning</p>
            </div>
          </div>
        </div>

        <div class="tabs__pane -tab-item-4">
          <div class="row y-gap-30 justify-center">
            <!-- Contenu pour Data Science -->
            <div class="col-12 text-center">
              <p>Contenu spécifique aux cours de Data Science</p>
            </div>
          </div>
        </div>

        <div class="tabs__pane -tab-item-5">
          <div class="row y-gap-30 justify-center">
            <!-- Contenu pour Python -->
            <div class="col-12 text-center">
              <p>Contenu spécifique aux cours de Python</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="text-center pt-60">
      <a href="#" class="button -md -purple-1 text-white">Voir tous les cours</a>
    </div>
  </div>
</section>
