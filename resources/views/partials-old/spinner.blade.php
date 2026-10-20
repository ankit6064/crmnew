<!-- HTML -->
<div class="loader-wrapper" id="spinner-overlay" style="display: none;">
    <div class="spinner" ></div>
</div>

<!-- CSS -->
<style>
.loader-wrapper{
  position: fixed;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255,255,255,0.7); /* optional bg */
  z-index: 9999;
}

.spinner{
  width:48px;
  height:48px;
  border:6px solid rgba(0,0,0,0.1);
  border-top-color:#111;
  border-radius:50%;
  animation:spin 0.8s linear infinite;
}

@keyframes spin{
  to { transform: rotate(360deg); }
}
</style>