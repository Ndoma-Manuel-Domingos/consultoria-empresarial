/**
 * ============================================================
 * SISTEMA DE CONSULTORIA
 * AJAX SYSTEM
 * ============================================================
 */

window.ajaxSystem = function () {

    return {

        /**
         * ====================================================
         * PROGRESS
         * ====================================================
         */

        progress: {

            visible: false,

            value: 0

        },


        /**
         * ====================================================
         * TOAST
         * ====================================================
         */

        toast: {

            visible: false,

            type: 'success',

            title: '',

            message: ''

        },


        /**
         * ====================================================
         * CONFIRM MODAL
         * ====================================================
         */

        confirm: {

            visible: false,

            title: '',

            message: '',

            callback: null,

            loading: false

        },


        /**
         * ====================================================
         * INIT
         * ====================================================
         */

        init() {

            console.log(
                '%c AJAX System iniciado ',
                'background:#2563eb;color:#fff;padding:5px 10px;border-radius:5px;'
            );

            this.bindForms();

            this.bindDeleteButtons();

        },


        /**
         * ====================================================
         * PROGRESS START
         * ====================================================
         */

        startProgress() {

            this.progress.visible = true;

            this.progress.value = 5;


            setTimeout(() => {

                if (this.progress.visible) {

                    this.progress.value = 25;

                }

            }, 150);


            setTimeout(() => {

                if (this.progress.visible) {

                    this.progress.value = 45;

                }

            }, 400);


            setTimeout(() => {

                if (this.progress.visible) {

                    this.progress.value = 65;

                }

            }, 700);


            setTimeout(() => {

                if (this.progress.visible) {

                    this.progress.value = 80;

                }

            }, 1000);

        },


        /**
         * ====================================================
         * PROGRESS FINISH
         * ====================================================
         */

        finishProgress() {

            this.progress.value = 100;


            setTimeout(() => {

                this.progress.visible = false;

                this.progress.value = 0;

            }, 350);

        },


        /**
         * ====================================================
         * PROGRESS ERROR
         * ====================================================
         */

        stopProgress() {

            this.progress.visible = false;

            this.progress.value = 0;

        },


        /**
         * ====================================================
         * TOAST
         * ====================================================
         */

        showToast(
            message,
            type = 'success',
            title = null
        ) {

            this.toast.type = type;

            this.toast.message = message;

            this.toast.title =
                title ||
                (
                    type === 'success'
                        ? 'Operação concluída'
                        : type === 'error'
                            ? 'Ocorreu um erro'
                            : 'Informação'
                );

            this.toast.visible = true;


            setTimeout(() => {

                this.toast.visible = false;

            }, 4000);

        },


        /**
         * ====================================================
         * CONFIRM
         * ====================================================
         */

        askConfirm(
            title,
            message,
            callback
        ) {

            this.confirm.title = title;

            this.confirm.message = message;

            this.confirm.callback = callback;

            this.confirm.loading = false;

            this.confirm.visible = true;

        },


        /**
         * ====================================================
         * CANCEL CONFIRM
         * ====================================================
         */

        cancelConfirm() {

            if (this.confirm.loading) {

                return;

            }


            this.confirm.visible = false;

            this.confirm.callback = null;

        },


        /**
         * ====================================================
         * CONFIRM ACTION
         * ====================================================
         */

        async confirmAction() {

            if (
                typeof this.confirm.callback !== 'function' ||
                this.confirm.loading
            ) {

                return;

            }


            this.confirm.loading = true;


            try {

                await this.confirm.callback();

            } finally {

                this.confirm.loading = false;

                this.confirm.visible = false;

                this.confirm.callback = null;

            }

        },


        /**
         * ====================================================
         * BIND FORMS
         * ====================================================
         */

        bindForms() {
            document.addEventListener( 'submit', async (event) => {
                const form = event.target;
                if (!(form instanceof HTMLFormElement)) {
                    return;
                }
                /**
                 * Formulários marcados como
                 * data-no-ajax não serão interceptados.
                */
                if (form.hasAttribute('data-no-ajax')) {
                    return;
                }
                event.preventDefault();
                await this.submitForm(form);
            });
        },


        /**
         * ====================================================
         * SUBMIT FORM
         * ====================================================
         */

        async submitForm(form) {

            if (form.dataset.loading === 'true') {

                return;

            }


            form.dataset.loading = 'true';


            this.startProgress();


            const submitButtons =
                form.querySelectorAll(
                    'button[type="submit"], input[type="submit"]'
                );


            const originalButtons = [];


            submitButtons.forEach(
                (button) => {

                    originalButtons.push({

                        element: button,

                        html: button.innerHTML,

                        disabled: button.disabled

                    });


                    button.disabled = true;


                    if (button.tagName === 'BUTTON') {

                        button.innerHTML = `
                            <i class="fas fa-spinner fa-spin mr-1"></i>
                            A processar...
                        `;

                    }

                }
            );


            try {

                const formData =
                    new FormData(form);


                const method =
                    (
                        form.querySelector(
                            'input[name="_method"]'
                        )?.value ||
                        form.method ||
                        'POST'
                    ).toUpperCase();


                const response =
                    await fetch(
                        form.action,
                        {

                            method:
                                method === 'GET'
                                    ? 'GET'
                                    : 'POST',

                            headers: {

                                'X-CSRF-TOKEN':
                                    document
                                        .querySelector(
                                            'meta[name="csrf-token"]'
                                        )
                                        .getAttribute('content'),

                                'X-Requested-With':
                                    'XMLHttpRequest',

                                'Accept':
                                    'application/json'

                            },

                            body:
                                method === 'GET'
                                    ? undefined
                                    : formData

                        }
                    );


                const contentType =
                    response.headers.get(
                        'content-type'
                    ) || '';


                let data = null;


                if (
                    contentType.includes(
                        'application/json'
                    )
                ) {

                    data =
                        await response.json();

                } else {

                    const text =
                        await response.text();

                    data = {

                        success:
                            response.ok,

                        message:
                            response.ok
                                ? 'Operação concluída com sucesso.'
                                : 'Ocorreu um erro ao processar o pedido.',

                        html: text

                    };

                }


                /**
                 * VALIDATION
                 */

                if (
                    response.status === 422
                ) {

                    this.stopProgress();

                    this.handleValidationErrors(
                        form,
                        data.errors || {}
                    );


                    this.showToast(
                        data.message ||
                            'Verifique os dados introduzidos.',
                        'error',
                        'Dados inválidos'
                    );


                    return;

                }


                /**
                 * ERRO HTTP
                 */

                if (!response.ok) {

                    throw new Error(
                        data.message ||
                        'Ocorreu um erro no servidor.'
                    );

                }


                /**
                 * SUCESSO
                 */

                this.finishProgress();


                this.clearValidationErrors(form);


                this.showToast(
                    data.message ||
                    'Operação realizada com sucesso.',
                    'success',
                    'Sucesso'
                );


                /**
                 * Se veio redirect,
                 * redireciona.
                 */

                if (data.redirect) {

                    setTimeout(() => {

                        window.location.href =
                            data.redirect;

                    }, 600);


                    return;

                }


                /**
                 * Atualiza HTML
                 */

                if (data.html) {

                    this.updateHtml(
                        data.html,
                        form
                    );

                }


                /**
                 * Callback personalizado
                 */

                if (
                    form.dataset.successCallback
                ) {

                    const callback =
                        window[
                            form.dataset.successCallback
                        ];


                    if (
                        typeof callback ===
                        'function'
                    ) {

                        callback(
                            data,
                            form
                        );

                    }

                }


            } catch (error) {

                console.error(
                    'AJAX Error:',
                    error
                );


                this.stopProgress();


                this.showToast(
                    error.message ||
                    'Não foi possível concluir a operação.',
                    'error',
                    'Erro'
                );

            } finally {

                form.dataset.loading = 'false';


                originalButtons.forEach(
                    (item) => {

                        item.element.disabled =
                            item.disabled;

                        item.element.innerHTML =
                            item.html;

                    }
                );

            }

        },


        /**
         * ====================================================
         * VALIDATION ERRORS
         * ====================================================
         */

        handleValidationErrors(
            form,
            errors
        ) {

            this.clearValidationErrors(form);


            Object.keys(errors).forEach(
                (field) => {

                    const input =
                        form.querySelector(
                            `[name="${field}"]`
                        );


                    if (!input) {

                        return;

                    }


                    input.classList.add(
                        'is-invalid'
                    );


                    const message =
                        Array.isArray(
                            errors[field]
                        )
                            ? errors[field][0]
                            : errors[field];


                    const feedback =
                        document.createElement(
                            'div'
                        );


                    feedback.className =
                        'invalid-feedback ajax-validation-error';


                    feedback.innerText =
                        message;


                    input.parentNode.appendChild(
                        feedback
                    );

                }
            );

        },


        /**
         * ====================================================
         * CLEAR VALIDATION
         * ====================================================
         */

        clearValidationErrors(form) {

            form
                .querySelectorAll(
                    '.is-invalid'
                )
                .forEach(
                    (element) => {

                        element.classList.remove(
                            'is-invalid'
                        );

                    }
                );


            form
                .querySelectorAll(
                    '.ajax-validation-error'
                )
                .forEach(
                    (element) => {

                        element.remove();

                    }
                );

        },


        /**
         * ====================================================
         * UPDATE HTML
         * ====================================================
         */

        updateHtml(
            html,
            form
        ) {

            const targetSelector =
                form.dataset.ajaxTarget;


            if (!targetSelector) {

                return;

            }


            const target =
                document.querySelector(
                    targetSelector
                );


            if (!target) {

                return;

            }


            target.innerHTML = html;

        },


        /**
         * ====================================================
         * DELETE BUTTONS
         * ====================================================
         */

        bindDeleteButtons() {

            document.addEventListener(
                'click',
                (event) => {

                    const button =
                        event.target.closest(
                            '[data-ajax-delete]'
                        );


                    if (!button) {

                        return;

                    }


                    event.preventDefault();


                    const form =
                        button.closest(
                            'form'
                        );


                    if (!form) {

                        console.error(
                            'Botão de eliminar sem formulário.'
                        );

                        return;

                    }


                    const title =
                        button.dataset.confirmTitle ||
                        'Eliminar registro';


                    const message =
                        button.dataset.confirmMessage ||
                        'Tem certeza que deseja eliminar este registro? Esta ação não pode ser desfeita.';


                    this.askConfirm(
                        title,
                        message,
                        async () => {

                            await this.submitForm(
                                form
                            );

                        }
                    );

                }
            );

        }

    };

};


/**
 * ============================================================
 * ALPINE READY
 * ============================================================
 */

document.addEventListener(
    'alpine:init',
    () => {
        console.log(
            'Alpine pronto.'
        );
    }
);
