<div class="md:grid md:grid-cols-3 md:gap-6">
    <div class="md:col-span-1">
        <div class="px-4 sm:px-0">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Datos de Facturación</h3>
            <p class="mt-1 text-sm text-gray-600">
                Información fiscal para la emisión de tus facturas (CFDI). Asegúrate de que coincida con tu Constancia de Situación Fiscal.
            </p>
        </div>
    </div>

    <div class="mt-5 md:mt-0 md:col-span-2">
        <form wire:submit.prevent="updateBillingInformation">
            <div class="shadow overflow-hidden sm:rounded-md">
                <div class="px-4 py-5 bg-white sm:p-6">
                    <div class="grid grid-cols-6 gap-6">
                        
                        <!-- Razón Social -->
                        <div class="col-span-6 sm:col-span-4">
                            <label for="razon_social" class="block text-sm font-medium text-gray-700">Razón Social / Nombre</label>
                            <input type="text" wire:model="razon_social" id="razon_social" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Ej. Despacho Jurídico S.C.">
                            @error('razon_social') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- RFC -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="rfc" class="block text-sm font-medium text-gray-700">RFC</label>
                            <input type="text" wire:model="rfc" id="rfc" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md uppercase" placeholder="XAXX010101000">
                            @error('rfc') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Régimen Fiscal -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="regimen_fiscal" class="block text-sm font-medium text-gray-700">Régimen Fiscal (Clave)</label>
                            <select wire:model="regimen_fiscal" id="regimen_fiscal" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Seleccione...</option>
                                <option value="601">601 - General de Ley Personas Morales</option>
                                <option value="603">603 - Personas Morales con Fines no Lucrativos</option>
                                <option value="605">605 - Sueldos y Salarios e Ingresos Asimilados a Salarios</option>
                                <option value="606">606 - Arrendamiento</option>
                                <option value="612">612 - Personas Físicas con Actividades Empresariales y Profesionales</option>
                                <option value="621">621 - Incorporación Fiscal</option>
                                <option value="626">626 - Régimen Simplificado de Confianza</option>
                            </select>
                            @error('regimen_fiscal') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Código Postal -->
                        <div class="col-span-6 sm:col-span-2">
                            <label for="codigo_postal" class="block text-sm font-medium text-gray-700">Código Postal</label>
                            <input type="text" wire:model="codigo_postal" id="codigo_postal" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" maxlength="5">
                            @error('codigo_postal') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                         <!-- Uso CFDI -->
                         <div class="col-span-6 sm:col-span-4">
                            <label for="uso_cfdi" class="block text-sm font-medium text-gray-700">Uso de CFDI</label>
                            <select wire:model="uso_cfdi" id="uso_cfdi" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="G03">G03 - Gastos en general</option>
                                <option value="D01">D01 - Honorarios médicos, dentales y gastos hospitalarios</option>
                                <option value="D02">D02 - Gastos médicos por incapacidad o discapacidad</option>
                                <option value="D04">D04 - Donativos</option>
                                <option value="I03">I03 - Equipo de transporte</option>
                                <option value="I04">I04 - Equipo de computo y accesorios</option>
                                <option value="P01">P01 - Por definir</option>
                                <option value="S01">S01 - Sin efectos fiscales</option>
                                <option value="CP01">CP01 - Pagos</option>
                                <option value="CN01">CN01 - Nómina</option>
                            </select>
                            @error('regimen_fiscal') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Correo Electrónico -->
                         <div class="col-span-6 sm:col-span-4">
                            <label for="email_facturacion" class="block text-sm font-medium text-gray-700">Correo para envío de Facturas</label>
                            <input type="email" wire:model="email_facturacion" id="email_facturacion" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('email_facturacion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                          <!-- Dirección Fiscal (Opcional) -->
                          <div class="col-span-6">
                            <label for="direccion_fiscal" class="block text-sm font-medium text-gray-700">Dirección Fiscal Completa (Opcional)</label>
                            <textarea wire:model="direccion_fiscal" id="direccion_fiscal" rows="3" class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md" placeholder="Calle, Número, Colonia, Municipio, Estado"></textarea>
                            <p class="mt-2 text-sm text-gray-500">
                                Para CFDI 4.0 el dato obligatorio es el Código Postal, pero puedes guardar la dirección completa para referencia.
                            </p>
                        </div>

                    </div>
                </div>
                
                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                    <div x-data="{ shown: false, timeout: null }"
                        x-init="@this.on('saved', () => { clearTimeout(timeout); shown = true; timeout = setTimeout(() => { shown = false }, 2000); })"
                        x-show.transition.out.opacity.duration.1500ms="shown"
                        x-transition:leave.opacity.duration.1500ms
                        style="display: none;"
                        class="text-sm text-gray-600 mr-3">
                        {{ __('Guardado.') }}
                    </div>

                    <button type="submit" wire:loading.attr="disabled" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                        <svg wire:loading class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Guardar Información
                    </button>
                    
                     <!-- Placeholder para Generar Factura (Futuro) -->
                     <button type="button" disabled class="ml-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                        Descargar Última Factura
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
