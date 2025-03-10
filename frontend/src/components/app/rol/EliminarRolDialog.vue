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

const errorExists = ref(null);
const successExists = ref(null);
const rolSeleccionadoRef = ref(null);

const eliminar = async () => {
  const id = rolSeleccionadoRef.value.id;

  try {
    const resp = await $api("/accesos/rol/eliminar/" + id, {
      method: "GET",
      onResponseError: ({ response }) => {
        errorExists.value = response._data.error;
      },
    });

    successExists.value = "Rol eliminado correctamente";
    setTimeout(async () => {
      emit("update:isDialogVisible", false);
      emit("listarRoles", true);
    }, 500);
  } catch (error) {
    errorExists.value = error;
  }
};

onMounted(() => {
  rolSeleccionadoRef.value = props.rolSeleccionado;
});
</script>

<template>
  <VDialog
    :model-value="props.isDialogVisible"
    persistent
    max-width="750"
    @update:model-value="dialogVisibleUpdate"
  >
    <VCard :title="'Eliminar Rol ' + rolSeleccionadoRef?.id">
      <DialogCloseBtn
        variant="text"
        size="default"
        @click="emit('update:isDialogVisible', false)"
      />
      <VCardText class="pa-5">
        <VAlert v-if="errorExists" type="error" class="mt-3">
          <strong>Error al procesar la solicitud en el servidor</strong>
        </VAlert>
        <VAlert v-if="successExists" type="warning" class="mt-3">
          <strong>{{ successExists }}</strong>
        </VAlert>
      </VCardText>

      <p v-if="rolSeleccionadoRef">
        ¿Estás seguro de eliminar el ROL "{{ rolSeleccionadoRef.nombre }}"
      </p>

      <VCardText class="pa-5">
        <VBtn color="error" class="mb-4" @click="eliminar"> Eliminar </VBtn>
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
