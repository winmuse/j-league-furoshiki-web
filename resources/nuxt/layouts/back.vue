<template>
  <div class="pg-container">
    <header class="header">
      <nav class="header-main navbar is-fixed-top" role="navigation" aria-label="main navigation">
        <div class="header-main-nav">
          <div>
            <div class="navbar-item">
              <div class="header-main-nav-arrow">
<!--                <span @click="$router.go(-1)"><b-icon icon="icon-font-arrow" /></span>-->
                  <nuxt-link :to="previousPage">
                      <b-icon icon="icon-font-arrow" />
                  </nuxt-link>
              </div>
            </div>
          </div>
        </div>
      </nav>
    </header>

    <section class="main-content">
      <nuxt />
    </section>
  </div>
</template>

<script>
    export default {
        computed: {
            previousPage() {
                return this.$store.state.prevPage;
            }
        }
    }
</script>
