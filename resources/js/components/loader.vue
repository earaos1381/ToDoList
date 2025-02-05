<template>
    <div class="loading-overlay">
        <div class="loader">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
</template>


<style>

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(156, 156, 156, 0.479);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

/* Estilo */

.loader {
  height: 120px;
  width: 120px;
  display: flex;
  justify-content: center;
  align-items: center;
  position: relative;
}

.loader div {
  height: 30px;
  width: 30px;
  position: absolute;
  animation: move 4s infinite;
}

.loader div:nth-child(1) {
  background-color: rgb(171, 10, 61);
  box-shadow: rgb(171, 10, 61) 0px 7px 29px 0px;
  transform: translate(-30px,-30px);
  animation-delay: -1s;
}

.loader div:nth-child(2) {
  background-color: rgb(182, 132, 0);
  box-shadow: rgb(182, 132, 0) 0px 7px 29px 0px;
  transform: translate(30px,-30px);
  animation-delay: -2s;
}

.loader div:nth-child(3) {
  background-color: rgb(68, 4, 18);
  box-shadow: rgb(68, 4, 18) 0px 7px 29px 0px;
  transform: translate(30px,30px);
  animation-delay: -3s;
}

.loader div:nth-child(4) {
  background-color: rgb(176, 171, 161);
  box-shadow: rgb(176, 171, 161) 0px 7px 29px 0px;
  transform: translate(-30px,30px);
  animation-delay: -4s;
}

@keyframes move {
  0% {
    transform: translate(-30px, -30px);
  }

  25% {
    transform: translate(30px, -30px);
  }

  50% {
    transform: translate(30px, 30px);
  }

  75% {
    transform: translate(-30px, 30px);
  }

  100% {
    transform: translate(-30px, -30px);
  }
}


</style>
