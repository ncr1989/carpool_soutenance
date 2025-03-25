export default [
    {
      path: "/login",
      name: "Login",
      component: () => import("../views/Login.vue"),
    },

    {
      path: "/accueil",
      name: "Accueil",
      component: () => import("../views/Accueil.vue"),
    },
    
  ];