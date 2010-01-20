#include <stdio.h>
#include <string.h>
#include <math.h>

#define          MAX_STACK_SIZE          100
#define          MAX_EXPR_SIZE           100

typedef enum {
	parensx,
	parendx,
	plus,
	meno,
	per,
	dividi,
	mod,
	eos,
	operando,
	separator
} precedenza;

int stack[MAX_STACK_SIZE];
char espr[MAX_EXPR_SIZE];
char espr_post[MAX_EXPR_SIZE];
const prd[] = { 0, 19, 12, 12, 13, 13, 13, 0};
const prf[] = {20, 19, 12, 12, 13, 13, 13, 0};

precedenza get_token(char *, int *, char *);
char print_token(precedenza);
void add(int *, int);
int delete(int *);
void postfix(void);
int valuta(int);

int main(int argc, char *argv[])
{
	int i;

	strcpy(espr, argv[1]);

	postfix();
	printf("%d", valuta(atoi(argv[2])));

	return 0;
}

void postfix(void)
{
	char simbolo, buf;
	precedenza token;
	int n = 0, top = 0, counter = -1;

	stack[0] = eos;
	for(token = get_token(&simbolo, &n, espr); token != eos; token = get_token(&simbolo, &n, espr)) {
		if(token == operando)
			espr_post[++counter] = simbolo;
		else if(token == parendx) {
			while(stack[top] != parensx) {
				buf = print_token(delete(&top));
				espr_post[++counter] = buf;
			}
			delete(&top);
		}
		else {
			while(prd[stack[top]] >= prf[token]) {
				buf = print_token(delete(&top));
				espr_post[++counter] = buf;
			}
 			if((espr_post[counter] - '0' >= 0) && (espr_post[counter] - '0' <= 9))
				espr_post[++counter] = '|';
			add(&top, token);
		}
	}
	while((buf = print_token(delete(&top))) != ' ')
		espr_post[++counter] = buf;
	espr_post[++counter] = '\0';
// 	printf("espressione post_fix: ");
// 	for(n = 0; n <= counter; n++)
// 		printf("%c ", espr_post[n]);
}

int valuta(int op)
{
	precedenza pred, token;
	char simbolo;
	int op1, op2, n = 0, top = -1, risultato, i = 0, numero = 0;

	pred = -1;
	token = get_token(&simbolo, &n, espr_post);
	while(token != eos) {
		if(token == operando) {
			numero = (simbolo - '0') + numero * 10;
			i++;
		} else if(token == separator) {
			add(&top, numero);
			numero = i = 0;
		} else {
			if(i > 0) {
				add(&top, numero);
				numero = i = 0;
			}
			op2 = delete(&top);
			op1 = delete(&top);
			switch(token) {
				case plus:   add(&top, op1 + op2); break;
				case meno:   add(&top, op1 - op2); break;
				case per:    add(&top, op1 * op2); break;
				case dividi: add(&top, op1 / op2); break;
				case mod:    add(&top, op1 % op2); break;
			}
		}
		pred = token;
		token = get_token(&simbolo, &n, espr_post);
	}

	risultato = delete(&top);
	if(op == 0) {
		srand(time(NULL));
		risultato += (-1 + rand() % 2) * (0 + rand() % (risultato/2));
	}

	return risultato;
}

void add(int *top, int item)
{
	if(*top >= MAX_STACK_SIZE - 1) {
		printf("\nLo stack è pieno.");
		return;
	}
	stack[++(*top)] = item;
}

int delete(int *top)
{
	if(*top == -1)
		return printf("\nLo stack è vuoto.");

	return stack[(*top)--];
}

precedenza get_token(char *simbolo, int *n, char *vett)
{
	*simbolo = vett[(*n)++];
	switch(*simbolo) {
		case '(':
			return parensx;
			break;
		case ')':
			return parendx;
			break;
		case '+':
			return plus;
			break;
		case '-':
			return meno;
			break;
		case '/':
			return dividi;
			break;
		case '*':
			return per;
			break;
		case '%':
			return mod;
			break;
		case ' ':
		case '\0':
			return eos;
			break;
		case '|':
			return separator;
		default :
			return operando;
			break;
	}
}

char print_token(precedenza token)
{
	switch(token) {
		case parensx:
			return '(';
			break;
		case parendx:
			return ')';
			break;
		case plus:
			return '+';
			break;
		case meno:
			return '-';
			break;
		case dividi:
			return '/';
			break;
		case per:
			return '*';
			break;
		case mod:
			return '%';
			break;
		case eos:
			return ' ';
			break;
		default:
			return token + '0';
			break;
	}
}
