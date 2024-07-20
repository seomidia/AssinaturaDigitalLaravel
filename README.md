
# Instalacao

Instale o pacote por *composer require seomidia/assinatura-digital*

# Configurar Providers

Em Config/app.php em providers coloque o seguinte namespace  *Seomidia\AssinaturaDigital\AssinaturaDigitalServiceProvider::class*

# .env

- CERT_PASSWORD="senha do certificado"
- CERT_PFX="nome-do-certificado.pfx"
- CERT_CRT="nome-do-certificado.crt"

# Seu certificado 

Seu arquivo .pfx deve esta no local especificado na variavel do env CERT_PFX, para quando for gerado o CERT_CRT seja feita a autenticacao correta com a senha que vc forneceu. Assim sendo possiver extrair a chave publica e a privada para ser utilizada no PDF.

# Metodo de assinatura 

em seu controlador de assinatura, informe use Seomidia\AssinaturaDigital\Signature;
para inserir o metodos no melhor momento de seu programa *new Signature($contrato,$cliente);* onde a variavel contrato sera um objeto do contrato e cliente um objeto do cliente

# storage

todos os contratos assinados seram armazenado em uma pasta contratos dentro da storage
