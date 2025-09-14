PAYMENT

stripe_payment_intent_id => $invoice->payment_intent
stripeInvoiceId => $invoice->id,

## IDEIA DE TABELA
CREATE TABLE pet_consult (
    id SERIAL PRIMARY KEY,
    pet_id INT NOT NULL REFERENCES pets(id), -- FK para o pet
    vet_id INT REFERENCES vets(id),          -- FK para o veterinário (se tiver essa tabela)
    consultation_date TIMESTAMP NOT NULL DEFAULT now(), -- data/hora da consulta
    
    reason TEXT,                -- motivo da consulta (ex: "tosse persistente")
    diagnosis TEXT,             -- diagnóstico do veterinário
    treatment TEXT,             -- tratamento indicado (ex: "antibiótico por 7 dias")
    prescription TEXT,          -- receita (separada do tratamento, se precisar detalhar medicamentos)
    notes TEXT,                 -- observações adicionais
    
    weight DECIMAL(5,2),        -- peso do pet no dia da consulta
    temperature DECIMAL(4,1),   -- temperatura corporal
    
    created_at TIMESTAMP DEFAULT now(),
    updated_at TIMESTAMP DEFAULT now()
);