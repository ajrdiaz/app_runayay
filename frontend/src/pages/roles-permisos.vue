<script setup>
import avatar1 from "@images/avatars/avatar-1.png";
import avatar2 from "@images/avatars/avatar-2.png";
import avatar3 from "@images/avatars/avatar-3.png";
import avatar4 from "@images/avatars/avatar-4.png";
import avatar5 from "@images/avatars/avatar-5.png";
import avatar6 from "@images/avatars/avatar-6.png";
import avatar7 from "@images/avatars/avatar-7.png";
import avatar8 from "@images/avatars/avatar-8.png";
import { ref } from "vue";

const data = ref([]);

const headers = [
  { title: "ID", key: "id" },
  { title: "ROL", key: "nombre" },
  { title: "PERMISOS", key: "permisos_nombre" },
  { title: "FECHA", key: "creado_en" },
  { title: "ACCIONES", key: "acciones" },
];

const searchQuery = ref("");
const isRegistrarRolDialogVisible = ref(false);
const isEliminarRolDialogVisible = ref(false);
const rolSeleccionado = ref(null);

const listar = async () => {
  const resp = await $api("/accesos/roles?buscar=" + searchQuery.value, {
    method: "GET",
    onResponseError: ({ response }) => {
      errorExists.value = response._data.error;
    },
  });

  data.value = resp.roles;
};

const registrar = (item = null) => {
  rolSeleccionado.value = item;
  isRegistrarRolDialogVisible.value = true;
};

const eliminar = (item) => {
  rolSeleccionado.value = item;
  isEliminarRolDialogVisible.value = true;
};

onMounted(() => {
  listar();
});

watch(isRegistrarRolDialogVisible, (event) => {
  if (event == false) {
    rolSeleccionado.value = null;
  }
});

watch(isEliminarRolDialogVisible, (event) => {
  if (event == false) {
    rolSeleccionado.value = null;
  }
});
</script>
<template>
  <div>
    <VCard title="Roles y Permisos">
      <VCardText class="d-flex flex-wrap gap-4">
        <div class="d-flex align-center">
          <!-- 👉 Search  -->
          <VTextField
            v-model="searchQuery"
            placeholder="Buscar Rol"
            style="inline-size: 300px"
            density="compact"
            class="me-3"
            @keyup.enter="listar"
          />
        </div>

        <VSpacer />

        <div class="d-flex gap-x-4 align-center">
          <VBtn color="primary" prepend-icon="ri-add-line" @click="registrar()">
            Agregar Rol
          </VBtn>
        </div>
      </VCardText>

      <VDataTable
        :headers="headers"
        :items="data"
        :items-per-page="5"
        class="text-no-wrap"
      >
        <template #item.id="{ item }">
          <span class="text-h6">{{ item.id }}</span>
        </template>
        <template #item.permisos_nombre="{ item }">
          <ul>
            <li v-for="permiso in item.permisos" :key="permiso.id">
              {{ permiso.nombre }}
            </li>
          </ul>
        </template>
        <template #item.acciones="{ item }">
          <div class="d-flex gap-1">
            <IconBtn size="small" @click="registrar(item)">
              <VIcon icon="ri-pencil-line" />
            </IconBtn>
            <IconBtn size="small" @click="eliminar(item)">
              <VIcon icon="ri-delete-bin-line" />
            </IconBtn>
          </div>
        </template>
      </VDataTable>
      <RegistrarRolDialog
        v-if="isRegistrarRolDialogVisible"
        v-model:is-dialog-visible="isRegistrarRolDialogVisible"
        :rolSeleccionado="rolSeleccionado"
        @listarRoles="listar"
      />
      <EliminarRolDialog
        v-if="isEliminarRolDialogVisible"
        v-model:is-dialog-visible="isEliminarRolDialogVisible"
        :rolSeleccionado="rolSeleccionado"
        @listarRoles="listar"
      />
    </VCard>
  </div>
</template>
