import { createRouter, createWebHistory } from "vue-router";
import authRoutes from "./auth";
import reservationRoutes from "./reservation";


const routes = [
  ...authRoutes,
  ...reservationRoutes
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});


router.beforeEach((to, from, next) => {
  const isAuthenticated = !!localStorage.getItem("jwt"); // Check if user is logged in
  if (to.meta.requiresAuth && !isAuthenticated) {
    next("/login"); 
  } else {
    next(); 
  }
});

export default router;
