<script setup>
import { onMounted, ref } from "vue";

const props = defineProps({
  isDialogVisible: {
    type: Boolean,
    required: true,
  },
  rolSeleccionado: {
    type: Object,
    required: false,
  },
});

const emit = defineEmits(["update:isDialogVisible", "listarRoles"]);

const dialogVisibleUpdate = (val) => {
  emit("update:isDialogVisible", val);
};

const LIST_PERMISOS = PERMISOS;

const rol = ref(null);
const permisos = ref([]);
const warning = ref(null);
const errorExists = ref(null);
const successExists = ref(null);
const rolSeleccionadoRef = ref(null);

const addPermiso = (permiso) => {
  let INDEX = permisos.value.findIndex((item) => item === permiso);
  if (INDEX !== -1) {
    permisos.value.splice(INDEX, 1);
  } else {
    permisos.value.push(permiso);
  }
};

const limpiarCampos = () => {
  rol.value = null;
  permisos.value = [];
  warning.value = null;
  errorExists.value = null;
  successExists.value = null;
  rolSeleccionadoRef.value = null;
};

const store = async () => {
  warning.value = null;
  if (!rol.value) {
    warning.value = "El campo rol es requerido";
    return;
  }

  if (permisos.value.length === 0) {
    warning.value = "Debe seleccionar al menos un permiso";
    return;
  }

  let data = {
    nombre: rol.value,
    permisos: permisos.value,
  };

  const id = rolSeleccionadoRef.value?.id ?? "";

  try {
    const resp = await $api("/accesos/roles/guardar/" + id, {
      method: "POST",
      body: data,
      onResponseError: ({ response }) => {
        errorExists.value = response._data.error;
      },
    });

    if (resp.codigo == 403) {
      warning.value = resp.mensaje;
    } else {
      successExists.value = "Rol creado correctamente";
      setTimeout(async () => {
        limpiarCampos();
        emit("update:isDialogVisible", false);
        emit("listarRoles", true);
      }, 500);
    }
  } catch (error) {
    errorExists.value = error;
  }
};

onMounted(() => {
  rolSeleccionadoRef.value = props.rolSeleccionado;
  if (rolSeleccionadoRef.value) {
    rol.value = rolSeleccionadoRef.value.nombre;
    permisos.value = rolSeleccionadoRef.value.permisos_nombre;
  }
});
</script>

<template>
  <VDialog
    :model-value="props.isDialogVisible"
    persistent
    max-width="750"
    @update:model-value="dialogVisibleUpdate"
  >
    <VCard
      :title="
        rolSeleccionadoRef
          ? 'Editar Rol ' + rolSeleccionadoRef.id
          : 'Agregar Rol'
      "
    >
      <DialogCloseBtn
        variant="text"
        size="default"
        @click="emit('update:isDialogVisible', false)"
      />
      <VCardText class="pa-5">
        <VTextField
          v-model="rol"
          label="Rol"
          placeholder="Ejemplo: Administrador"
        />
        <VAlert v-if="warning" type="warning" class="mt-3">
          <strong>{{ warning }}</strong>
        </VAlert>
        <VAlert v-if="errorExists" type="error" class="mt-3">
          <strong>Error al procesar la solicitud en el servidor</strong>
        </VAlert>
        <VAlert v-if="successExists" type="success" class="mt-3">
          <strong>{{ successExists }}</strong>
        </VAlert>
      </VCardText>

      <VCardText class="pa-5">
        <VBtn color="primary" class="mb-4" @click="store">
          {{ !rolSeleccionadoRef ? "Crear" : "Editar" }}
        </VBtn>
        <VTable>
          <thead>
            <tr>
              <th class="text-uppercase">Módulo</th>
              <th class="text-uppercase">Permisos</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(item, index) in LIST_PERMISOS" :key="index">
              <td>
                {{ item.name }}
              </td>
              <td>
                <ul>
                  <li
                    v-for="(permiso, index2) in item.permisos"
                    :key="index2"
                    class="list-none"
                  >
                    <VCheckbox
                      v-model="permisos"
                      :label="permiso.name"
                      :value="permiso.permiso"
                      @click="addPermiso(permiso.permiso)"
                    />
                  </li>
                </ul>
              </td>
            </tr>
          </tbody>
        </VTable>
      </VCardText>
    </VCard>
  </VDialog>
</template>

<style lang="scss">
.refer-link-input {
  .v-field--appended {
    padding-inline-end: 0;
  }

  .v-field__append-inner {
    padding-block-start: 0.125rem;
  }
}
</style>
